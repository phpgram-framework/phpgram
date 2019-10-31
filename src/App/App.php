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

/** @version 1.2.8 */

namespace Gram\App;

use Gram\ResolverCreator\ResolverCreator;
use Gram\ResolverCreator\ResolverCreatorInterface;
use Gram\Middleware\Handler\ResponseCreator;
use Gram\Middleware\Handler\NotFoundHandler;
use Gram\Middleware\RouteMiddleware;
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

	protected $stdStrategy=null, $resolverCreator=null, $options=[];

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
				$this->options,
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

		try {
			$response = $this->handle($request);  //Starte den Stack und erstelle Response
		} catch (\Exception $e) {
			$stream = $this->streamFactory->createStream("<h1>Application Error</h1> <pre>".$e."</pre>");

			$response = $this->responseFactory->createResponse(500)->withBody($stream);
		}

		$emitter = new Emitter();

		$emitter->emit($response);	//Gebe Header und Body vom Response aus
	}

	/**
	 * @inheritdoc
	 * @throws \Exception
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$response = $this->queueHandler->handle($request);	//Starte den Stack und erstelle Response

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
		//setze Standard Objekte
		$resolverCreator = $this->resolverCreator ?? new ResolverCreator();
		$stdStrategy= $this->stdStrategy ?? new StdAppStrategy();

		//Wird am Ende ausgeführt um den Response zu erstellen
		//erhält Factory um Response zu erstellen
		$this->responseCreator = $this->responseCreator ?? new ResponseCreator(
				$this->responseFactory,
				$this->streamFactory,
				$resolverCreator,
				$stdStrategy,
				$this->container
			);

		$this->queueHandler = $this->queueHandler ?? new QueueHandler($this->responseCreator,$this->container);	//default Fallback
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

		//Füge Routing Middleware hinzu
		$routeMiddleware = $this->routeMiddleware ?? new RouteMiddleware(
				$this->getRouter(),		//router für den request
				new NotFoundHandler($this->responseCreator),	//error handler
				$this,
				$this->getStrategyCollector()
			);

		//füge Route in der mitte hinzu
		$this->queueHandler->add($routeMiddleware);
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

	public function setFactory(ResponseFactoryInterface $responseFactory,StreamFactoryInterface $streamFactory)
	{
		$this->responseFactory=$responseFactory;
		$this->streamFactory=$streamFactory;
	}

	public function setStrategy(StrategyInterface $stdStrategy=null)
	{
		$this->stdStrategy=$stdStrategy;
	}

	public function setResolverCreator(ResolverCreatorInterface $creator=null)
	{
		$this->resolverCreator=$creator;
	}

	public function setLastHandler(RequestHandlerInterface $responseCreator=null)
	{
		$this->responseCreator = $responseCreator;
	}

	public function setQueueHandler(RequestHandlerInterface $queueHandler=null)
	{
		$this->queueHandler = $queueHandler;
	}

	public function setRouteMiddleware(MiddlewareInterface $routeMw)
	{
		$this->routeMiddleware = $routeMw;
	}

	public function setContainer(ContainerInterface $container=null)
	{
		$this->container = $container;
	}

	public function setOptions(array $options=[])
	{
		$this->options=$options;
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

	public function addMiddle($middleware,$order=null)
	{
		$this->getMWCollector()->addStd($middleware,$order);
		return $this;
	}
}