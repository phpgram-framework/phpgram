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

/** @version 1.2.4 */

namespace Gram\App;

use Gram\ResolverCreator\ResolverCreator;
use Gram\ResolverCreator\ResolverCreatorInterface;
use Gram\Middleware\Handler\ResponseCreator;
use Gram\Middleware\Handler\NotFoundHandler;
use Gram\Middleware\RouteMiddleware;
use Gram\Route\Collector\RouteCollectorTrait;
use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Collector\StrategyCollector;
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
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class App
 * @package Gram\App
 *
 * Startet die Seite und führt die Middleware aus
 */
class App
{
	use RouteCollectorTrait;

	protected $router=null,$middlewareCollector=null,$strategyCollector=null,$container=null;
	protected $stdStrategy=null,$resolverCreator=null,$options=[],$responseCreator=null,$queuHandler=null;

	/** @var ResponseFactoryInterface */
	protected $responseFactory;

	/** @var StreamFactoryInterface */
	protected $streamFactory;

	private static $_instance;

	/**
	 * App constructor.
	 * Private da die App nur mit init gestartet werden darf
	 */
	private function __construct(){}

	/**
	 * Erstellt ein Routerobjekt zurück wenn es noch nicht erstellt wurde
	 *
	 * @return Router|null
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
	 * @return MiddlewareCollector|null
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
	 * @return StrategyCollector|null
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
		try {
			$response = $this->sendRequest($request);  //Starte den Stack und erstelle Response
		} catch (\Exception $e) {
			$stream = $this->streamFactory->createStream("<h1>Application Error</h1> <pre>".$e."</pre>");

			$response = $this->responseFactory->createResponse(500)->withBody($stream);
		}

		$emitter = new Emitter();

		$emitter->emit($response);	//Gebe Header und Body vom Response aus
	}

	/**
	 * Eine Psr 18 Varainte mit ServerRequestInterface anstatt RequestInterface
	 * Achtung: Ohne Überprüfung von Request und Response
	 *
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 * @throws \Exception
	 */
	public function sendRequest(ServerRequestInterface $request): ResponseInterface
	{
		$queue = $this->init();

		$response = $queue->handle($request);	//Starte den Stack und erstelle Response

		return $response;
	}

	/**
	 * Setzt alle wichtigen Handler und Middleware.
	 * Startet den QueueHandler der die Abfolge der Middlewares verwaltet
	 *
	 * Füge mit der Routing Middleware weitere Middleware aus dem Collector hinzu die
	 * zuerst gestartet werden sollen
	 *
	 * Füge dann die Routing Middleware dem Stack hinzu
	 *
	 * Gebe zum Schluss den fertigen QueueHandler zurück
	 *
	 * @return QueueHandler
	 */
	protected function init()
	{
		//setze Standard Objekte
		$resolverCreator = $this->resolverCreator ?? new ResolverCreator();
		$stdStrategy= $this->stdStrategy ?? new StdAppStrategy();

		//bereite Queue vor
		//Wird am Ende ausgeführt um den Response zu erstellen
		//erhält Factory um Response zu erstellen
		$fallback = $this->responseCreator ?? new ResponseCreator(
				$this->responseFactory,
				$this->streamFactory,
				$resolverCreator,
				$stdStrategy,
				$this->container
			);

		$queue = $this->queuHandler ?? new QueueHandler($fallback,$this->container);	//default Fallback

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

		return $queue;
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
		$this->queuHandler = $queueHandler;
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

	//Middleware

	public function addMiddle($middleware,$order=null)
	{
		$this->getMWCollector()->addStd($middleware,$order);
		return $this;
	}
}