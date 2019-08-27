<?php
namespace Gram\Route;
use Gram\Route\Map\MiddlewareMap;
use Gram\Route\Map\RouteMap;
use Gram\Route\Dispatcher\Dispatcher;
use Gram\Route\Dispatcher\StaticDispatcher;
use Gram\Route\Dispatcher\DynamicDispatcher;

class Router
{
	const REQUEST_ROUTER=1;
	const BEFORE_MIDDLEWARE=2;
	const AFTER_MIDDLEWARE=3;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const OK = 200;

	private $routertyp,$checkMethod,$map,$uri,$handle,$param=array(),$status;
	private static $routemap=null;

	public function __construct(int $routertyp,$checkMethod=true){
		$this->routertyp=$routertyp;
		$this->checkMethod=$checkMethod;

		/*
		 * Routemap immer erstellen, da diese weitere Middleware definitionen haben kann,
		 * die sonst nicht geladen werden wenn middleware vor dem router aufgerufen wird
		 */
		if(!isset(self::$routemap)){
			self::$routemap=new RouteMap();
			self::$routemap->getMap();
		}

		//suche Map zum Dispatchen
		switch ($routertyp){
			case self::REQUEST_ROUTER:
				$this->map=self::$routemap;
				break;
			case self::BEFORE_MIDDLEWARE:
				$this->map=new MiddlewareMap('before');
				break;
			case self::AFTER_MIDDLEWARE:
				$this->map=new MiddlewareMap('after');
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
		if($this->doDisptach(new StaticDispatcher($this->map))){
			return true;
		}

		//Danach die dynamischen
		if($this->doDisptach(new DynamicDispatcher($this->map))){
			return true;
		}

		$this->status=self::NOT_FOUND;

		if(isset($this->map->getMap()['er404'])){
			$this->handle['callback']=$this->map->getMap()['er404'];
		}
		return false;
	}

	private function doDisptach(Dispatcher $dispatcher){
		$response = $dispatcher->dispatch($this->uri);

		if($response[0]===Dispatcher::FOUND){
			$this->handle=$response[1];
			$this->param=$response[2];

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
		$this->handle['callback']=$this->map->getMap()['erNotAllowed'];
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
	 * @return MiddlewareMap
	 */
	public function getMap(){
		return $this->map;
	}
}