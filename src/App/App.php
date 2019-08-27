<?php
namespace Gram\App;
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

	public static $options;
	private static $_instance;
	public static $responseFactory,$streamFactory;

	/**
	 * Start der Seite
	 * Ruft den Router auf und nimmt die auszuführende Klasse mit der Funktion und den Middlewares entgegen
	 * Führt zuerst die Middlewares aus die mit beforeM gekennzeichnet sind (diese sollen vor dem Seitenaufruf erfolgen
	 * Danach wird der eigentliche Seitencontroller und deren Funktion aufgerufen
	 * Zum Schluss werden noch alle Middlewares durchgegangen die mit afterM gekennzeichnet sind
	 * @param ServerRequestInterface $request
	 */
	public function start(ServerRequestInterface $request){
		$uri=$request->getUri()->getPath();
		$method=$request->getMethod();

		//before

		$router2=new Router(Router::BEFORE_MIDDLEWARE);

		if(isset($router2->getMap()->getMap()['std'])){
			$stdBefore=$router2->getMap()->getMap()['std'];
		}else{
			$stdBefore=array();
		}

		$router2->run($uri);

		if(isset($router2->getHandle()['callback'])){
			$mstack=$router2->getHandle()['callback'];
		}else{
			$mstack=array();
		}

		$stack=array_merge($stdBefore,$mstack);


		if(!empty($stack)){
			$caller2=new CallableCreator(null,$stack);
			$callable2=$caller2->getCallable();

			foreach ($callable2 as $item) {

				debug_page($item);

				echo $item->callback(array(),$request);
			}
		}
		

		
		//request


		$router=new Router(Router::REQUEST_ROUTER);

		$router->run($uri,$method);

		$request=$request->withAttribute("handle",$router->getHandle());
		$request=$request->withAttribute("param",$router->getParam());


		$handle=$request->getAttribute("handle");
		$param=$request->getAttribute("param");

		$caller = new CallableCreator($handle['callback']);

		echo $callable=$caller->getCallable()->callback($param,$request);


		//after
/*

		$router2=new Router(Router::AFTER_MIDDLEWARE);

		$router2->run($uri);


		$caller2=new CallableCreator(null,$router2->getHandle()['callback']);
		$callable2=$caller2->getCallable();

		//debug_page($callable2);
*/
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


	public static function init(ResponseFactoryInterface $responseFactory,StreamFactoryInterface $streamFactory) {
		if(!isset(self::$_instance)) {
			self::$_instance = new self();
		}

		self::$responseFactory=$responseFactory;
		self::$streamFactory=$streamFactory;

		return self::$_instance;
	}
}