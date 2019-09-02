<?php
namespace Gram\Route\Dispatcher;
use Gram\Route\Interfaces\DispatcherInterface;

abstract class Dispatcher implements DispatcherInterface
{
	private $routes;

	public function setData(array $routes){
		$this->routes=$routes;
	}

	public function dispatch($uri){
		if(isset($this->routes['static'][$uri])){
			return [self::FOUND,$this->routes['static'][$uri],[]];
		}

		//wenn es keine Dnymic Routes gibt
		if(!isset($this->routes['dynamic'])){
			return [self::NOT_FOUND];
		}

		return $this->dispatchDynamic(
			$uri,
			$this->routes['dynamic']['regexes'],
			$this->routes['dynamic']['dynamichandler']
		);
	}


}