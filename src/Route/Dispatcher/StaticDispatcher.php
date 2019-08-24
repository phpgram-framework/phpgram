<?php
namespace Gram\Route\Dispatcher;
use Gram\Route\Map\Map;

class StaticDispatcher implements Dispatcher
{
	private $routes,$handler;

	public function __construct(Map $map){
		$map=$map->getMap();
		$this->routes=$map['staticroutes'];
		$this->handler=$map['statichandler'];
	}

	public function dispatch($uri){
		foreach ($this->routes as $i=>$route) {
			if($route===$uri){
				return array(self::FOUND,$this->handler[$i],array());
			}
		}

		return array(self::NOT_FOUND);
	}
}