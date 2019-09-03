<?php
namespace Gram\App;
use Gram\Middleware\Handler\ResponseHandler;
use Gram\Middleware\Handler\NotFoundHandler;
use Gram\Middleware\RouteMiddleware;
use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Collector\StrategyCollector;
use Gram\Route\Router;
use Gram\Strategy\StdAppStrategy;
use Gram\Strategy\StrategyInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Class App
 * @package Napf
 * @author Jörn Heinemann
 * Startet die Seite und führt die Middlewares aus
 */
class App
{
	private $request,$router=null,$middlewareCollector=null,$strategyCollector=null;

	private static $_instance,$stdStrategy=null,$options=[];
	private static $responseFactory, $streamFactory;

	/**
	 * App constructor.
	 * Private da die App nur mit init gestartet werden darf
	 */
	private function __construct()
	{
	}

	public function getRouter()
	{
		if(!isset($this->router)){

			$this->router = new Router(
				true,
				self::$options,
				$this->getMWCollector(),
				$this->getStrategyCollector()
			);
		}

		return $this->router;
	}

	public function getMWCollector()
	{
		if(!isset($this->middlewareCollector)){
			$this->middlewareCollector = new MiddlewareCollector();
		}

		return $this->middlewareCollector;
	}

	public function getStrategyCollector()
	{
		if(!isset($this->strategyCollector)){
			$this->strategyCollector = new StrategyCollector();
		}

		return $this->strategyCollector;
	}

	/**
	 * Start der Seite
	 * Ruft den Router auf und nimmt die auszuführende Klasse mit der Funktion und den Middlewares entgegen
	 * Führt zuerst die Middlewares aus die mit beforeM gekennzeichnet sind (diese sollen vor dem Seitenaufruf erfolgen
	 * Danach wird der eigentliche Seitencontroller und deren Funktion aufgerufen
	 * Zum Schluss werden noch alle Middlewares durchgegangen die mit afterM gekennzeichnet sind
	 * @param ServerRequestInterface $request
	 */
	public function start(ServerRequestInterface $request)
	{
		$this->request=$request;

		$stdStrategy= self::$stdStrategy ?? new StdAppStrategy();

		//bereite Queue vor
		$fallback = new ResponseHandler(self::$responseFactory,self::$streamFactory,$stdStrategy);	//erstellt das callable aus dem requests

		$queue = new QueueHandler($fallback);	//default Fallback

		//___________________________________________________________________________

		$routingMiddleware = new RouteMiddleware(
			$this->getRouter(),		//router für den request
			new NotFoundHandler($fallback),	//error handler
			$queue,
			$this->getMWCollector(),
			$this->getStrategyCollector()
		);

		//Erstelle Middleware Stack
		//Über die Routing Middleware, da diese die Funktion auch noch braucht

		$routingMiddleware->buildStack(true);

		//füge Route in der mitte hinzu
		$queue->add($routingMiddleware);

		//______________________________________________________________________________

		//Starte den Stack und erstelle Response

		$response = $queue->handle($request);

		$emitter = new Emitter();

		$emitter->emit($response);
	}

	public static function app()
	{
		if(!isset(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function setFactory(ResponseFactoryInterface $responseFactory,StreamFactoryInterface $streamFactory)
	{
		self::$responseFactory=$responseFactory;
		self::$streamFactory=$streamFactory;
	}

	public static function setStrategy(StrategyInterface $stdStrategy=null)
	{
		self::$stdStrategy=$stdStrategy;
	}

	public static function setOptions(array $options=[])
	{
		self::$options=$options;
	}
}