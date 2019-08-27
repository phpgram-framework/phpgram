<?php
namespace Gram\Route\Dispatcher;
use Gram\Route\Map\Map;

class StaticDispatcher implements Dispatcher
{
	private $routes=array();

	public function __construct(Map $map){
		$map=$map->getMap();
		if(isset($map['staticroutes'])){
			$this->routes=$map['staticroutes'];
		}
	}

	public function dispatch($uri){
		if(isset($this->routes[$uri])){
			return array(self::FOUND,$this->routes[$uri],array());
		}

		return array(self::NOT_FOUND);
	}
}