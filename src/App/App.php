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

/** @version 1.4.0 */

namespace Gram\App;

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

	protected $stdStrategy=null, $resolverCreator=null, $router_options=[], $raw_options = [];

	/** @var ContainerInterface */
	protected $container=null;

	/** @var RouterInterface */
	protected $router=null;

	/** @var QueueHandler */
	protected $queueHandler = null;

	/** @var MiddlewareInterface */
	protected $routeMiddleware=null;

	/** @var RequestHandlerInterface */
	protected $responseCreator=null;

	/** @var ResponseFactoryInterface */
	protected $responseFactory;

	/** @var StreamFactoryInterface */
	protected $streamFactory;

	/** @var MiddlewareCollectorInterface */
	protected $middlewareCollector=null;

	/** @var StrategyCollectorInterface */
	protected $strategyCollector=null;

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
			$stream = $this->streamFactory->createStream("<h1>Application Error</h1> <pre>".$e."</pre>");

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
	protected function build()
	{
		//setze Default Raw Options
		$this->raw_options +=[
			'response_creator'=>'Gram\\Middleware\\Handler\\ResponseCreator',
			'queue_handler'=>'Gram\\App\\QueueHandler',
			'routeMw'=>'Gram\\Middleware\\RouteMiddleware'
		];

		//setze Standard Objekte
		$resolverCreator = $this->resolverCreator ?? new ResolverCreator();
		$stdStrategy= $this->stdStrategy ?? new StdAppStrategy();

		//Wird am Ende ausgeführt um den Response zu erstellen
		//erhält Factory um Response zu erstellen
		$responseCreator = $this->responseCreator ?? new $this->raw_options['response_creator'] (
				$this->responseFactory,
				$this->streamFactory,
				$resolverCreator,
				$stdStrategy,
				$this->container
			);

		$this->queueHandler = $this->queueHandler ?? new $this->raw_options['queue_handler']($responseCreator,$this->container);	//default Fallback

		$this->routeMiddleware = $this->routeMiddleware ?? new $this->raw_options['routeMw'](
				$this->getRouter(),		//router für den request
				new NotFoundHandler($responseCreator),	//error handler
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
	protected function init()
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
	 * check_method: bool
	 * slash_trim: bool
	 * caching: bool
	 * cache: string
	 * dispatcher: Gram\\Route\\Dispatcher\\DynamicDispatcher
	 * generator: Gram\\Route\\Generator\\DynamicGenerator
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