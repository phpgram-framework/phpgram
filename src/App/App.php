<?php
namespace Gram\App;
use Gram\Middleware\Handler\CallbackHandler;
use Gram\Middleware\Handler\NotFoundHandler;
use Gram\Middleware\Handler\QueueHandler;
use Gram\Middleware\RouteMiddleware;
use Gram\Route\Router;
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
	private $path,$method; //old pre psr 7
	private $request,$uri;


	public static $options;
	private static $_instance;
	private static $responseFactory, $streamFactory;

	/**
	 * App constructor.
	 * Private da die App nur mit init gestartet werden darf
	 */
	private function __construct(){}

	/**
	 * Start der Seite
	 * Ruft den Router auf und nimmt die auszuführende Klasse mit der Funktion und den Middlewares entgegen
	 * Führt zuerst die Middlewares aus die mit beforeM gekennzeichnet sind (diese sollen vor dem Seitenaufruf erfolgen
	 * Danach wird der eigentliche Seitencontroller und deren Funktion aufgerufen
	 * Zum Schluss werden noch alle Middlewares durchgegangen die mit afterM gekennzeichnet sind
	 * @param ServerRequestInterface $request
	 */
	public function start(ServerRequestInterface $request){
		$this->request=$request;

		//bereite Queue vor
		$fallback = new CallbackHandler(self::$responseFactory,self::$streamFactory);	//erstellt das callable aus dem requests

		$this->uri = $request->getUri()->getPath();

		$queue = new QueueHandler($fallback);	//default Fallback

		//___________________________________________________________________________

		//Erstelle Middleware Stack

		//erstelle Before Middleware
		$this->buildStack($queue,new Router(Router::BEFORE_MIDDLEWARE));

		//füge Route in der mitte hinzu
		$queue->add(new RouteMiddleware(
			new Router(Router::REQUEST_ROUTER),		//router für den request
			new NotFoundHandler(self::$responseFactory,self::$streamFactory)	//error handler
		));

		//erstelle After Middleware
		$this->buildStack($queue,new Router(Router::AFTER_MIDDLEWARE));

		//______________________________________________________________________________

		//Starte den Stack und erstelle Response

		$response = $queue->handle($request);

		$content = $response->getBody()->__toString();

		echo $content;	//TODO Strategies zum Output verwenden z. B. echo, return json, return xml, etc.
	}

	private function buildStack(QueueHandler $queueHandler,Router $router){
		$std=array();
		$mstack=array();
		$callable=array();

		//hole die Standard Middlewares, die imemr ausgeführt werden sollen
		if(isset($router->getMap()->getMap()['std'])){
			$std=$router->getMap()->getMap()['std'];
		}

		//hole die Dynamischen Middlewares die nur bei bestimmten Seiten ausgeführt werden sollen
		$router->run($this->uri);

		if(isset($router->getHandle()['callback'])){
			$mstack=$router->getHandle()['callback'];
		}

		//verbinde beide zu einem Stack
		$stack=array_merge($std,$mstack);

		if(!empty($stack)){
			$creator=new CallableCreator(null,$stack);
			$callable=$creator->getCallable();
		}

		//füge den Stack dem Queue Handler hinzu
		foreach ($callable as $item) {
			$queueHandler->add($item->callback());
		}
	}

	/**
	 * Hole die Url
	 * und löscht den letzen /
	 * Ohne Psr
	 */
	private function parseUrl(){
		$uri=$_SERVER['REQUEST_URI'];
		$url=parse_url($uri);

		//Startseite
		if(!isset($url['path']) || $url['path']=="/"){
			$this->path="/";
		}else{
			$this->path=rtrim($url['path'],"/");
		}
		$GLOBALS['url']=$uri;	//für referer

		$this->method=$_SERVER['REQUEST_METHOD'];
	}


	public static function init(ResponseFactoryInterface $responseFactory,StreamFactoryInterface $streamFactory,$options=array()) {
		if(!isset(self::$_instance)) {
			self::$_instance = new self();
		}

		self::$responseFactory=$responseFactory;
		self::$streamFactory=$streamFactory;

		Router::setOptions($options);

		return self::$_instance;
	}
}