<?php
namespace Gram\Route\Dispatcher;
use Gram\Route\Interfaces\Map;

/**
 * Class StaticDispatcher
 * @package Gram\Route\Dispatcher
 * @author Jörn Heinemann
 * Dispatcher Klasse für Routes ohne Variablen
 */
class StaticDispatcher implements \Gram\Route\Interfaces\Components\StaticDispatcher
{
	private $routes=array();

	public function setMap(Map $map){
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