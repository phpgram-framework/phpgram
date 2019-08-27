<?php
namespace Gram\Route\Map;
use Gram\App\App;
use Gram\Route\Collector\MiddlewareCollector;

/**
 * Class MiddlewareMap
 * @package Gram\Route\Map
 * @author Jörn Heinemann
 * Die Map für die Middleware
 */
class MiddlewareMap extends Map
{
	protected $typ;
	protected static $callbacks=array(),$cachefile=null;

	public function __construct($typ){
		$this->cache=self::$cachefile[$typ];
		$this->typ=$typ;
	}

	protected function createMap(){
		$routes=MiddlewareCollector::middle($this->typ);	//init Collector

		call_user_func(self::$callbacks[$this->typ]);	//ruft die richtigen middlewares auf (before oder after)

		$routes->trigger();
		$this->map=$routes->map();
	}

	public static function map(callable $routes,$typ="",$caching=false,$cache=""){
		self::$callbacks[$typ]=$routes;

		if($caching){
			self::$cachefile[$typ]=$cache;
		}
	}
}