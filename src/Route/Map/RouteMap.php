<?php
namespace Gram\Route\Map;
use Gram\Route\Collector\RouteCollector;

/**
 * Class RouteMap
 * @package Gram\Route\Map
 * @author Jörn Heinemann
 * Die Map für die normalen Routes
 */
class RouteMap extends BaseMap implements \Gram\Route\Interfaces\Components\RouteMap
{
	protected static $callbacks=array(),$cachefile=null;	//Alle Route Sammler

	public function __construct(){
		$this->cache=self::$cachefile;
	}

	protected function createMap(){
		$routes=RouteCollector::route();	//init Collector

		//Durchlaufe alle Routesammler
		foreach (self::$callbacks as $callback) {
			call_user_func($callback);
		}

		$routes->trigger();
		$this->map=$routes->map();
	}

	public static function map(callable $routes,$type="",$caching=false,$cache=""){
		self::$callbacks[]=$routes;
		if($caching){
			self::$cachefile=$cache;
		}
	}

	public function get404(){
		return parent::getValue('er404');
	}

	public function get405(){
		return parent::getValue('er405');
	}
}