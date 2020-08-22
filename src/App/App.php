<?php
/**
 * phpgram
 *
 * This File is part of the phpgram Micro Framework
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

/** @version 1.8.1 */

namespace Gram\App;

use Gram\Middleware\QueueHandler;
use Gram\Middleware\Queue\QueueInterface;
use Gram\Middleware\QueueHandlerInterface;
use Gram\Middleware\ResponseCreator;
use Gram\Middleware\RouteMiddleware;
use Gram\ResolverCreator\ResolverCreator;
use Gram\ResolverCreator\ResolverCreatorInterface;
use Gram\Middleware\Handler\NotFoundHandler;
use Gram\Route\Collector\RouteCollectorTrait;
use Gram\Route\Interfaces\RouterInterface;
use Gram\Route\Route;
use Gram\Route\RouteGroup;
use Gram\Route\Router;
use Gram\Strategy\StdAppStrategy;
use Gram\Strategy\StrategyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class App
 * @package Gram\App
 *
 * Startet die Seite und führt die Middleware aus
 */
class App implements RequestHandlerInterface
{
	use RouteCollectorTrait;

	protected $router_options = [], $debug_mode = true;

	/** @var array */
	protected $middleware = [];

	/** @var StrategyInterface */
	protected $stdStrategy;

	/** @var ResolverCreatorInterface */
	protected $resolverCreator;

	/** @var ContainerInterface|null */
	protected $container;

	/** @var RouterInterface */
	protected $router;

	/** @var QueueHandlerInterface */
	protected $queueHandler;

	/** @var MiddlewareInterface */
	protected $routeMiddleware;

	/** @var RequestHandlerInterface */
	protected $responseCreator;

	/** @var ResponseFactoryInterface */
	protected $responseFactory;

	/**
	 * Welche Queue erstellt werden soll bei jedem Request
	 *
	 * @var string
	 */
	protected $queueClass = \Gram\Middleware\Queue\Queue::class;

	/** @var self */
	private static $_instance;

	/**
	 * Erstellt ein Routerobjekt wenn es noch nicht erstellt wurde
	 *
	 * @return RouterInterface
	 */
	public function getRouter()
	{
		if(!isset($this->router)){
			$this->router = new Router($this->router_options);
		}

		return $this->router;
	}

	/**
	 * Gibt den ResponseCreator zurück
	 *
	 * Sollte es diesen nicht geben wird er erstellt
	 *
	 * Factories und ggf. ResolverCreator sowie Strategy sollten vorher gesetzt werden
	 *
	 * @return RequestHandlerInterface
	 */
	public function getResponseCreator()
	{
		if (!isset($this->responseCreator)) {
			//Wird am Ende ausgeführt um den Response zu erstellen
			//erhält Factory um Response zu erstellen
			$this->responseCreator = new ResponseCreator (
					$this->responseFactory,
					$this->resolverCreator ?? new ResolverCreator(),
					$this->stdStrategy ?? new StdAppStrategy(),
					$this->container
				);
		}

		return $this->responseCreator;
	}

	/**
	 * Start der Seite, führe build aus um das framework zu initialisieren
	 *
	 * Erhält den Request
	 *
	 * erstellt den Response und gibt diesen zurück
	 *
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 */
	public function start(ServerRequestInterface $request)
	{
		$this->build();

		return $this->handle($request);
	}

	/**
	 * @inheritdoc
	 *
	 * führt die Mw Stack aus
	 *
	 * fängt Exception ab und gibt diese gerendert aus
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$request = $this->init($request);	//füge Std Middleware hinzu

		try {
			$response = $this->queueHandler->handle($request); //Starte den Stack und erstelle Response
		} catch (\Exception $e) {
			if($this->debug_mode === true) {
				$content = "<h1>Application Error</h1> <pre>".$e."</pre>";
			}else{
				$content = "";
			}

			if($e instanceof \Gram\Exceptions\PageNotFoundException) {
				$status = 404;
			} elseif ($e instanceof \Gram\Exceptions\PageNotAllowedException) {
				$status = 405;
			} else {
				$status = 500;
			}

			$response = $this->responseFactory->createResponse($status);

			$response->getBody()->write($content);
		}

		return $response;
	}

	/**
	 * Setze alle long life Objects:
	 *
	 * Setzt alle wichtigen Handler und Middleware.
	 * Startet den QueueHandler der die Abfolge der Middlewares verwaltet
	 *
	 * @return void
	 */
	public function build()
	{
		$this->queueHandler = $this->queueHandler ?? new QueueHandler(
			$this->getResponseCreator(), //default Fallback
			$this->container
			);

		$this->routeMiddleware = $this->routeMiddleware ?? new RouteMiddleware(
				$this->getRouter(),		//router für den request
				new NotFoundHandler($this->getResponseCreator()),	//error handler
				$this
			);
	}

	/**
	 * Füge zuerst alle Std Mw hinzu
	 * die zuerst aufgerufen werden sollen
	 *
	 * Füge dann die Routing Middleware dem Stack hinzu
	 *
	 * Packe dann die Queue in den Request
	 *
	 * @see build() muss vorher ausgeführt sein!
	 *
	 * @param ServerRequestInterface $request
	 * @return ServerRequestInterface
	 */
	public function init(ServerRequestInterface $request)
	{
		/** @var QueueInterface $queue */
		$queue = new $this->queueClass;

		//Erstelle Middleware Stack
		foreach ($this->middleware as $item) {
			$queue->add($item);
		}

		//füge Route in der mitte hinzu
		$queue->add($this->routeMiddleware);

		return $request->withAttribute(QueueInterface::class,$queue);
	}

	/**
	 * Erstellt bzw. erweiterte den Middleware Stack
	 *
	 * wenn eine routeid und groupid gegeben sind für diese die Mw hinzufügen
	 *
	 * @param ServerRequestInterface $request
	 * @param int|null $routeid
	 * @param array|null $groupid
	 */
	public function buildStack(ServerRequestInterface $request, int $routeid = null, array $groupid = null)
	{
		if($routeid === null || $groupid === null){
			return;
		}

		/** @var QueueInterface $queue */
		$queue = $this->queueHandler->getQueue($request);

		foreach ($groupid as $item) {
			$groupMw = RouteGroup::getMiddleware($item);
			//Füge Routegroup Mw hinzu
			if ($groupMw !== null){
				foreach ($groupMw as $item2) {
					$queue->add($item2);
				}
			}
		}

		$routeMw = Route::getMiddleware($routeid);
		//Füge Route MW hinzu
		if($routeMw !== null){
			foreach ($routeMw as $item) {
				$queue->add($item);
			}
		}
	}

	//Optionen

	/**
	 * Setze Psr 17 Response Factory
	 *
	 * Wird für Response Creator benötigt
	 *
	 * @param ResponseFactoryInterface $responseFactory
	 */
	public function setFactory(ResponseFactoryInterface $responseFactory)
	{
		$this->responseFactory = $responseFactory;
	}

	/**
	 * Setzt die Standard Strategy
	 *
	 * Wenn keine gesetzt: @see StdAppStrategy
	 *
	 * @param StrategyInterface $stdStrategy
	 */
	public function setStrategy(StrategyInterface $stdStrategy)
	{
		$this->stdStrategy = $stdStrategy;
	}

	/**
	 * Setzt den Standard Resolver Creator um das Callable aus den Routes um zuformen
	 *
	 * Wenn keiner gesetzt: @see ResolverCreator
	 *
	 * @param ResolverCreatorInterface $creator
	 */
	public function setResolverCreator(ResolverCreatorInterface $creator)
	{
		$this->resolverCreator = $creator;
	}

	/**
	 * Setzt den RequestHandler der am Ende der Middleware Kette ausgeführt werden soll
	 *
	 * Standard: @see ResponseCreator
	 *
	 * @param RequestHandlerInterface $responseCreator
	 */
	public function setLastHandler(RequestHandlerInterface $responseCreator)
	{
		$this->responseCreator = $responseCreator;
	}

	/**
	 * Setzt den RequestHandler der die Middleware abfolge überwacht
	 *
	 * Standard: @see QueueHandler
	 *
	 * @param QueueHandlerInterface $queueHandler
	 */
	public function setQueueHandler(QueueHandlerInterface $queueHandler)
	{
		$this->queueHandler = $queueHandler;
	}

	/**
	 * Bestimmt welches Queue object bei jedem Request erzeugt werden soll
	 *
	 * Class name muss angegeben werden, da die Klasse, bei jedem Request,
	 * immer ein neues Object erzeugen muss
	 *
	 * @param string $queueClass
	 */
	public function setQueueClass(string $queueClass)
	{
		$this->queueClass = $queueClass;
	}

	/**
	 * Setzt die RouteMiddleware
	 *
	 * Standard: @see RouteMiddleware
	 *
	 * @param MiddlewareInterface $routeMw
	 */
	public function setRouteMiddleware(MiddlewareInterface $routeMw)
	{
		$this->routeMiddleware = $routeMw;
	}

	/**
	 * Setzt den Psr 11 Container
	 *
	 * @param ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * Gibt den Psr 11 Container wieder zurück
	 *
	 * @return ContainerInterface
	 */
	public function getContainer(): ContainerInterface
	{
		return $this->container;
	}

	/**
	 * Bestimmte welcher Router genutzt werden soll
	 *
	 * @param RouterInterface $router
	 */
	public function setRouter(RouterInterface $router)
	{
		$this->router = $router;
	}

	/**
	 * Setzt Optionen für den Router:
	 *
	 * slash_trim: bool
	 * caching: bool
	 * cache: string
	 * dispatcher: Gram\\Route\\Dispatcher\\MarkBased
	 * generator: Gram\\Route\\Generator\\MarkBased
	 * parser: Gram\\Route\\Parser\\StdParser
	 * collector: Gram\\Route\\Collector\\RouteCollector
	 *
	 * @param array $options
	 */
	public function setRouterOptions(array $options = [])
	{
		$this->router_options = $options;
	}

	/**
	 * Entscheidet wie mit Exceptions verfahren werden soll:
	 *
	 * true = Exception wird angezeigt
	 * false = ein leerer Response mit 500 wird ausgegeben
	 *
	 * @param bool $type
	 */
	public function debugMode(bool $type = true)
	{
		$this->debug_mode = $type;
	}

	//Routes

	/**
	 * @inheritdoc
	 */
	public function add(string $path,$handler,array $method): Route
	{
		return $this->getRouter()->getCollector()->add($path,$handler,$method);
	}

	/**
	 * @inheritdoc
	 */
	public function group($prefix,callable $groupcollector): RouteGroup
	{
		return $this->getRouter()->getCollector()->group($prefix,$groupcollector);
	}

	//Spezielle Routes

	/**
	 * Setze den Handler für 404
	 *
	 * Wenn kein handler angegeben wird eine Exception geworfen
	 *
	 * @param $handle
	 */
	public function set404($handle)
	{
		$this->getRouter()->getCollector()->set404($handle);
	}

	/**
	 * Setze  den Handler für 405
	 *
	 * Hier wird wie bei @see set404 sonst auch eine Exception geworfen
	 *
	 * @param $handle
	 */
	public function set405($handle)
	{
		$this->getRouter()->getCollector()->set405($handle);
	}

	/**
	 * Setze den base path der vor jede Route gesetzt wird
	 *
	 * @param string $base
	 */
	public function setBase(string $base)
	{
		$this->getRouter()->getCollector()->setBase($base);
	}

	/**
	 * Gebe den base path wieder zurück
	 *
	 * @return string
	 */
	public function getBase()
	{
		return $this->getRouter()->getCollector()->getBase();
	}

	//Middleware

	/**
	 * Füge eine Middleware hinzu die unabhänig von den Routes ausgeführt werden soll
	 *
	 * @param $middleware
	 * @return $this
	 */
	public function addMiddleware($middleware)
	{
		$this->middleware[] = $middleware;
		return $this;
	}

	/**
	 * App wird als Singleton aufgerufen
	 *
	 * @return App
	 */
	public static function app()
	{
		if(!isset(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}