<?php
namespace Gram\Route\Map;
use Gram\Route\Collector\MiddlewareCollector;

/**
 * Class MiddlewareMap
 * @package Gram\Route\Map
 * @author Jörn Heinemann
 * Die Map für die Middleware
 */
class MiddlewareMap extends Map
{
	protected $type;

	public function __construct($options,$type){
		parent::init($options);
		$this->type=$type;
	}

	protected function createMap(){
		$routes=MiddlewareCollector::middle(true,$this->type,$this->options['definePaths'],$this->options['userPlaceholders']);
		$this->map=$routes->map();
	}
}