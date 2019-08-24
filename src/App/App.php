<?php
namespace Gram\App;
require_once(GRAMCONFIG . "routes/registermiddle.php");

use Gram\Route\Route;
use Gram\Route\Router\RouterRoute;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

/**
 * Class App
 * @package Napf
 * @author Jörn Heinemann
 * Startet die Seite und führt die Middlewares aus
 */
class App
{
	private $handle,$result,$options;

	private $path,$method; //old pre psr 7

	private static $middlewares=array(), $stdbeforemiddle=array(), $stdaftermiddle=array();

	public function __construct(array $options){
		$this->options=$options;
	}

	/**
	 * Start der Seite
	 * Ruft den Router auf und nimmt die auszuführende Klasse mit der Funktion und den Middlewares entgegen
	 * Führt zuerst die Middlewares aus die mit beforeM gekennzeichnet sind (diese sollen vor dem Seitenaufruf erfolgen
	 * Danach wird der eigentliche Seitencontroller und deren Funktion aufgerufen
	 * Zum Schluss werden noch alle Middlewares durchgegangen die mit afterM gekennzeichnet sind
	 */
	public function start(){
		//pre psr 7
		if(!$this->options['psr']){
			$this->startOld();
			return;
		}

		//psr 7
		$psr17Factory=new Psr17Factory();

		$request=new ServerRequestCreator($psr17Factory,$psr17Factory,$psr17Factory,$psr17Factory);
		$request=$request->fromGlobals();

		$router=new Route($this->options['routing']['routes']);
		$caller=new CallableAdapter();
		$response=$router->process($request,$caller);

		echo $response->getBody();
	}

	//old pre psr 7
	public function startOld(){
		$this->parseUrl();
		$router = new RouterRoute($this->options['routing']['routes']);
		$route = $router->normalRun($this->path,$this->method);


		$this->handle=(object)$route['handle'];	//hier sind alle Anweisungen von der definieren Route drin

		$callback=$this->handle->callback();

		$this->result=call_user_func_array($callback,$route['param']);

		echo $this->result;
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
			$this->path=rtrim(urldecode($url['path']),"/");
		}
		$GLOBALS['url']=$uri;	//für referer

		$this->method=$_SERVER['REQUEST_METHOD'];
	}


	/**
	 * Hier werden die verfügbaren Middlewares gespeichert
	 * @param array $handle
	 */
	public static function registerMiddle($handle=array()){
		array_push(self::$middlewares,$handle);
	}

	/**
	 * Die Standard Output Middleware. Diese wird aufgerufen wenn keine Outputmiddleware angegeben wurde
	 * @param array $handle
	 */
	public static function setStandardOut($handle=array()){
		self::$stdaftermiddle=$handle;
	}
}