<?php
namespace Gram\Route\Collector;

class MiddlewareCollector extends Collector
{
	private static $_instance,$type;

	public static function before($route,array $stack){
		//nur hinzufügen wenn die before Middlewares benötigt werden
		if(self::$type=="before"){
			self::middle()->set($route,$stack);
		}

		return self::middle();
	}

	public static function after($route,array $stack){
		if(self::$type=="after"){
			self::middle()->set($route,$stack);
		}

		return self::middle();
	}

	public static function getInstance() {
		return new MiddlewareCollector;
	}

	public static function middle($start=false,$typ="",$paths=array(),$placeholders=array()) {
		if(!isset(self::$_instance)) {
			self::$_instance = self::getInstance();
			self::$userplaceholders=$placeholders;
			self::$type=$typ;

			if(!empty($paths)){
				//Hole die Routes aus den Config Dateien
				foreach ($paths as $path) {
					if (file_exists($path)) {
						include_once($path);
					}
				}
			}
		}

		if($start){
			self::$_instance->trigger();
		}

		return self::$_instance;
	}
}