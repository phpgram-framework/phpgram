<?php
namespace Gram\Route;
use Gram\Route\Interfaces\Components\MiddlewareMap;
use Gram\Route\Interfaces\Components\RouteMap;
use Gram\Route\Interfaces\Dispatcher;
use Gram\Route\Interfaces\Components\DynamicDispatcher;
use Gram\Route\Interfaces\Components\StaticDispatcher;
use Gram\Route\Interfaces\Map;
use Gram\Route\Collector\BaseCollector;

class Router
{
	const REQUEST_ROUTER=1;
	const BEFORE_MIDDLEWARE=2;
	const AFTER_MIDDLEWARE=3;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const OK = 200;

	private $routertyp,$checkMethod,$map,$uri,$handle,$param=array(),$status;
	private static $routemap,$beforemap,$aftermap,$routeinitmap=false;

	//Options
	private static $dynamicDispatcher,$staticDispatcher;

	public function __construct(int $routertyp,$checkMethod=true){
		$this->routertyp=$routertyp;
		$this->checkMethod=$checkMethod;

		//suche Map zum Dispatchen
		switch ($routertyp){
			case self::REQUEST_ROUTER:
				$this->map=self::$routemap;
				break;
			case self::BEFORE_MIDDLEWARE:
				$this->map=self::$beforemap;
				break;
			case self::AFTER_MIDDLEWARE:
				$this->map=self::$aftermap;
		}
	}

	public function run($uri,$httpMethod=null){
		$this->uri=urldecode($uri);	//umlaute filtern

		//laufe durch alle Dispatcher
		if(!$this->disptach()){
			return false;
		}

		if(!isset($httpMethod) || !$this->checkMethod){
			return true;
		}

		return $this->checkMethod($httpMethod);		//Prüfe die Method bei normalen Request
	}

	private function disptach(){
		//Prüfe zuerst die Static Routes
		if($this->doDisptach(self::$staticDispatcher)){
			return true;
		}

		//Danach die dynamischen
		if($this->doDisptach(self::$dynamicDispatcher)){
			return true;
		}

		$this->status=self::NOT_FOUND;

		if($this->map instanceof RouteMap){
			$map=$this->map->get404();

			if(isset($map)){
				$this->handle['callback']=$map;
			}
		}

		return false;
	}

	private function doDisptach(Dispatcher $dispatcher){
		$dispatcher->setMap($this->map);
		$response = $dispatcher->dispatch($this->uri);

		if($response[0]===Dispatcher::FOUND){
			$this->handle=$response[1];
			$this->param=$response[2];
			$this->status=self::OK;
			return true;
		}

		return false;
	}

	private function checkMethod($httpMethod){
		//Prüfe ob der Request mit der richtigen Methode durchgeführt wurde
		foreach ((array)$this->handle['method'] as $item) {
			if(strtolower($httpMethod)===strtolower($item)){
				return true;
			}
		}

		$this->status=self::METHOD_NOT_ALLOWED;

		if($this->map instanceof RouteMap){
			$map=$this->map->get405();

			if(isset($map)){
				$this->handle['callback']=$map;
			}
		}

		return false;
	}

	/**
	 * @return mixed
	 */
	public function getHandle(){
		return $this->handle;
	}

	/**
	 * @return mixed
	 */
	public function getParam(){
		return $this->param;
	}

	/**
	 * @return mixed
	 */
	public function getStatus(){
		return $this->status;
	}


	/**
	 * @return Map
	 */
	public function getMap(){
		return $this->map;
	}

	public static function setDispatcher(StaticDispatcher $staticDispatcher,DynamicDispatcher $dynamicDispatcher){
		self::$staticDispatcher=$staticDispatcher;
		self::$dynamicDispatcher=$dynamicDispatcher;
	}

	public static function setMaps(RouteMap $routeMap,MiddlewareMap $before,MiddlewareMap $after){
		self::$routemap=$routeMap;

		/*
		 * Routemap immer erstellen, da diese weitere Middleware definitionen haben kann,
		 * die sonst nicht geladen werden wenn middleware vor dem router aufgerufen wird
		 */
		if(!self::$routeinitmap){
			self::$routemap->initMap();
			self::$routeinitmap=true;
		}

		self::$beforemap=$before;
		self::$aftermap=$after;
	}

	public static function setOptions($options=array()){
		$options +=array(
			'disdynamic'=>'Gram\\Route\Dispatcher\\DynamicDispatcher',
			'disstatic'=>'Gram\\Route\\Dispatcher\\StaticDispatcher',
			'gendynamic'=>'Gram\\Route\\Generator\\DynamicGenerator',
			'genstatic'=>'Gram\\Route\\Generator\\StaticGenerator',
			'parser'=>'Gram\\Route\\Parser\\StdParser',
			'routemap'=>'Gram\\Route\\Map\\RouteMap',
			'middlemap'=>'Gram\\Route\\Map\\MiddlewareMap',
		);

		Route::setParser(new $options['parser']);
		BaseCollector::setGenerator(new $options['genstatic'],new $options['gendynamic']);
		self::setDispatcher(new $options['disstatic'],new $options['disdynamic']);
		self::setMaps(new $options['routemap'],new $options['middlemap']('before'),new $options['middlemap']('after'));
	}
}