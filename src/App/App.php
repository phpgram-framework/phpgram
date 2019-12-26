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

/** @version 1.5.0 */

namespace Gram\App;

use Gram\Middleware\QueueHandler;
use Gram\ResolverCreator\ResolverCreator;
use Gram\ResolverCreator\ResolverCreatorInterface;
use Gram\Middleware\Handler\NotFoundHandler;
use Gram\Route\Collector\RouteCollectorTrait;
use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Collector\StrategyCollector;
use Gram\Route\Interfaces\MiddlewareCollectorInterface;
use Gram\Route\Interfaces\RouterInterface;
use Gram\Route\Interfaces\StrategyCollectorInterface;
use Gram\Route\Route;
use Gram\Route\RouteGroup;
use Gram\Route\Router;
use Gram\Strategy\StdAppStrategy;
use Gram\Strategy\StrategyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
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

	protected $router_options=[], $raw_options = [], $debug_mode = 0;

	/** @var StrategyInterface */
	protected $stdStrategy;

	/** @var ResolverCreatorInterface */
	protected $resolverCreator;

	/** @var ContainerInterface|null */
	protected $container;

	/** @var RouterInterface */
	protected $router;

	/** @var QueueHandler */
	protected $queueHandler;

	/** @var MiddlewareInterface */
	protected $routeMiddleware;

	/** @var RequestHandlerInterface */
	protected $responseCreator;

	/** @var ResponseFactoryInterface */
	protected $responseFactory;

	/** @var StreamFactoryInterface */
	protected $streamFactory;

	/** @var MiddlewareCollectorInterface */
	protected $middlewareCollector;

	/** @var StrategyCollectorInterface */
	protected $strategyCollector;

	/** @var self */
	private static $_instance;

	/**
	 * App constructor.
	 * Private da die App nur mit init gestartet werden darf
	 */
	private function __construct(){}

	/**
	 * Erstellt ein Routerobjekt zurück wenn es noch nicht erstellt wurde
	 *
	 * @return RouterInterface
	 */
	public function getRouter()
	{
		if(!isset($this->router)){

			$this->router = new Router(
				$this->router_options,
				$this->getMWCollector(),
				$this->getStrategyCollector()
			);
		}

		return $this->router;
	}

	/**
	 * Gibt einen Middlewarecollector zurück
	 *
	 * @return MiddlewareCollectorInterface
	 */
	public function getMWCollector()
	{
		if(!isset($this->middlewareCollector)){
			$this->middlewareCollector = new MiddlewareCollector();
		}

		return $this->middlewareCollector;
	}

	/**
	 * Gibt ein Strategy Collector zurück
	 *
	 * @return StrategyCollectorInterface
	 */
	public function getStrategyCollector()
	{
		if(!isset($this->strategyCollector)){
			$this->strategyCollector = new StrategyCollector();
		}

		return $this->strategyCollector;
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
		if (isset($this->responseCreator)) {
			return $this->responseCreator;
		}

		$this->raw_options +=[
			'response_creator'=>'Gram\\Middleware\\ResponseCreator'
		];

		//setze Standard Objekte
		$resolverCreator = $this->resolverCreator ?? new ResolverCreator();
		$stdStrategy= $this->stdStrategy ?? new StdAppStrategy();

		//Wird am Ende ausgeführt um den Response zu erstellen
		//erhält Factory um Response zu erstellen
		$this->responseCreator = $this->responseCreator ?? new $this->raw_options['response_creator'] (
				$this->responseFactory,
				$this->streamFactory,
				$resolverCreator,
				$stdStrategy,
				$this->container
			);

		return $this->responseCreator;
	}

	/**
	 * Start der Seite
	 *
	 * Erhält den Request
	 *
	 * erstellt den Response und emittet ihn
	 *
	 * @param ServerRequestInterface $request
	 */
	public function start(ServerRequestInterface $request)
	{
		$this->build();

		$this->init();

		$response = $this->handle($request);

		$emitter = new Emitter();

		$emitter->emit($response);	//Gebe Header und Body vom Response aus
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
		try {
			$response = $this->queueHandler->handle($request); //Starte den Stack und erstelle Response
		} catch (\Exception $e) {
			if($this->debug_mode === 0) {
				$content = "<h1>Application Error</h1> <pre>".$e."</pre>";
			}elseif ($this->debug_mode === 1){
				$content = "<h1>Application Error</h1>";
			}else{
				$content = "";
			}

			$stream = $this->streamFactory->createStream($content);

			$response = $this->responseFactory->createResponse(500)->withBody($stream);
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
		//setze Default Raw Options
		$this->raw_options +=[
			'queue_handler'=>'Gram\\Middleware\\QueueHandler',
			'routeMw'=>'Gram\\Middleware\\RouteMiddleware'
		];

		$this->queueHandler = $this->queueHandler ?? new $this->raw_options['queue_handler'](
			$this->getResponseCreator(), //default Fallback
			$this->container
			);

		$this->routeMiddleware = $this->routeMiddleware ?? new $this->raw_options['routeMw'](
				$this->getRouter(),		//router für den request
				new NotFoundHandler($this->getResponseCreator()),	//error handler
				$this,
				$this->getStrategyCollector()
			);
	}

	/**
	 * Füge zuerst alle Std Mw hinzu
	 * die zuerst aufgerufen werden sollen
	 *
	 * Füge dann die Routing Middleware dem Stack hinzu
	 *
	 */
	public function init()
	{
		//Erstelle Middleware Stack

		foreach ($this->middlewareCollector->getStdMiddleware() as $item) {
			$this->queueHandler->add($item);
		}

		//füge Route in der mitte hinzu
		$this->queueHandler->add($this->routeMiddleware);
	}

	/**
	 * Erstellt bzw. erweiterte den Middleware Stack
	 *
	 * wenn eine routeid und groupid gegeben sind für diese die Mw hinzufügen
	 *
	 * @param int|null $routeid
	 * @param array|null $groupid
	 */
	public function buildStack(int $routeid=null, array $groupid = null)
	{
		if($routeid===null || $groupid===null){
			return;
		}

		foreach ($groupid as $item) {
			$grouMw=$this->middlewareCollector->getGroup($item);
			//Füge Routegroup Mw hinzu
			if ($grouMw!==null){
				foreach ($grouMw as $item2) {
					$this->queueHandler->add($item2);
				}
			}
		}

		$routeMw = $this->middlewareCollector->getRoute($routeid);
		//Füge Route MW hinzu
		if($routeMw!==null){
			foreach ($routeMw as $item) {
				$this->queueHandler->add($item);
			}
		}
	}

	public static function app()
	{
		if(!isset(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	//Optionen

	/**
	 * Setze Psr 17 Response und Stream Factory
	 *
	 * Wird für Response Creator benötigt
	 *
	 * @param ResponseFactoryInterface $responseFactory
	 * @param StreamFactoryInterface $streamFactory
	 */
	public function setFactory(ResponseFactoryInterface $responseFactory,StreamFactoryInterface $streamFactory)
	{
		$this->responseFactory=$responseFactory;
		$this->streamFactory=$streamFactory;
	}

	/**
	 * Setzt die Standard Strategy
	 *
	 * Wenn keine gesetzt: @see StdAppStrategy
	 *
	 * @param StrategyInterface|null $stdStrategy
	 */
	public function setStrategy(StrategyInterface $stdStrategy=null)
	{
		$this->stdStrategy=$stdStrategy;
	}

	/**
	 * Setzt den Standard Resolver Creator um das Callable aus den Routes um zuformen
	 *
	 * Wenn keiner gesetzt: @see ResolverCreator
	 *
	 * @param ResolverCreatorInterface|null $creator
	 */
	public function setResolverCreator(ResolverCreatorInterface $creator=null)
	{
		$this->resolverCreator=$creator;
	}

	/**
	 * Setzt den RequestHandler der am Ende der Middleware Kette ausgeführt werden soll
	 *
	 * Standard: @see ResponseCreator
	 *
	 * @param RequestHandlerInterface|null $responseCreator
	 */
	public function setLastHandler(RequestHandlerInterface $responseCreator=null)
	{
		$this->responseCreator = $responseCreator;
	}

	/**
	 * Setzt den RequestHandler der die Middleware abfolge überwacht
	 *
	 * Standard: @see QueueHandler
	 *
	 * @param RequestHandlerInterface|null $queueHandler
	 */
	public function setQueueHandler(RequestHandlerInterface $queueHandler=null)
	{
		$this->queueHandler = $queueHandler;
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
	 * @param ContainerInterface|null $container
	 */
	public function setContainer(ContainerInterface $container=null)
	{
		$this->container = $container;
	}

	/**
	 * Setzt Optionen für den Router:
	 *
	 * slash_trim: bool
	 * caching: bool
	 * cache: string
	 * dispatcher: Gram\\Route\\Dispatcher\\Std\\GroupCountBased
	 * generator: Gram\\Route\\Generator\\Std\\GroupCountBased
	 * parser: Gram\\Route\\Parser\\StdParser
	 * collector: Gram\\Route\\Collector\\RouteCollector
	 *
	 * @param array $options
	 */
	public function setRouterOptions(array $options=[])
	{
		$this->router_options=$options;
	}

	/**
	 * Entscheidet wie mit Exceptions verfahren werden soll:
	 *
	 * 0 = Exception vollständig ausgeben (für dev)
	 * 1 = Nur Anzeigen, dass es einen Error gab, keine Exception ausgeben
	 * 2 = Nur Status 500 ausgeben
	 *
	 * @param int $type
	 */
	public function debugMode($type = 0)
	{
		$this->debug_mode = $type;
	}

	/**
	 * Setze die Klassen die in @see build() erstellt werden sollen
	 *
	 * Kann auch durch bereits bestehende Objects überschrieben werden
	 *
	 * @param array $options
	 */
	public function setRawOptions(array $options=[])
	{
		$this->raw_options = $options;
	}

	//Routes

	public function add(string $path,$handler,array $method):Route
	{
		return $this->getRouter()->getCollector()->add($path,$handler,$method);
	}

	public function addGroup($prefix,callable $groupcollector):RouteGroup
	{
		return $this->getRouter()->getCollector()->addGroup($prefix,$groupcollector);
	}

	//Spezielle Routes

	public function set404($handle)
	{
		$this->getRouter()->getCollector()->set404($handle);
	}

	public function set405($handle)
	{
		$this->getRouter()->getCollector()->set405($handle);
	}

	public function setBase(string $base)
	{
		$this->getRouter()->getCollector()->setBase($base);
	}

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
	public function addMiddle($middleware)
	{
		$this->getMWCollector()->addStd($middleware);
		return $this;
	}
}