<?php
namespace Gram\Route\Map;
use Gram\Route\Collector\RouteCollector;

/**
 * Class RouteMap
 * @package Gram\Route\Map
 * @author Jörn Heinemann
 * Die Map für die normalen Routes
 */
class RouteMap extends Map
{
	public function __construct($options){
		parent::init($options);
	}

	protected function createMap(){
		$routes=RouteCollector::route(true,$this->options['definePaths'],$this->options['userPlaceholders']);
		$this->map=$routes->map();
	}
}