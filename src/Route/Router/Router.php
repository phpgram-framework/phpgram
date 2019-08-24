<?php
namespace Gram\Route\Router;
use Gram\Route\Dispatcher\StaticDispatcher;
use Gram\Route\Handler\Handler;
use Gram\Route\Map\Map;
use Gram\Route\Dispatcher\Dispatcher;
use Gram\Route\Dispatcher\DynamicDispatcher;
/**
 * Class Router
 * @package Gram\Lib\Route
 * @author Jörn Heinemann
 * Hauptklasse für alle Router
 * Sucht die richtige Route anhand der aufgerufenen Url mithilfe eines Dispatcher
 */
abstract class Router
{
	protected $handle,$param=array(),$status;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const OK = 200;

	protected function tryDispatch($uri,Map $map){
		//Prüfe zuerst die Static Routes
		$dispatcher=new StaticDispatcher($map);
		$response = $dispatcher->dispatch($uri);

		if($this->checkDispatch($response[0])){
			$this->handle=$response[1];
			$this->param=$response[2];
			return true;
		}

		//prüfe die Dynamic Routes
		$dispatcher=new DynamicDispatcher($map);
		$response = $dispatcher->dispatch($uri);

		if($this->checkDispatch($response[0])){
			$this->handle=$response[1];
			$this->param=$response[2];
			return true;
		}

		$this->status=self::NOT_FOUND;
		return false;
	}

	protected function checkDispatch($response){
		switch ($response){
			case Dispatcher::NOT_FOUND:
				return false;
			case Dispatcher::FOUND:
				return true;
			default:
				return false;
		}
	}

	/**
	 * Prüfe ob die Route mit der richtigen Methode aufgerufen wurde
	 * @param $httpMethod
	 * @return bool
	 */
	protected function checkMethod($httpMethod){
		//Prüfe ob der Request mit der richtigen Methode durchgeführt wurde
		foreach ((array)$this->handle['m'] as $item) {
			if(strtolower($httpMethod)===strtolower($item)){
				return true;
			}
		}

		$this->status=self::METHOD_NOT_ALLOWED;
		return false;
	}

	public function getStatus(){
		return $this->status;
	}

	public function getHandle(){
		return $this->handle;
	}

	public function getParam(){
		return $this->param;
	}
}