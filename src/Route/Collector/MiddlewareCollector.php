<?php
namespace Gram\Route\Collector;

class MiddlewareCollector extends BaseCollector implements \Gram\Route\Interfaces\Components\MiddlewareCollector
{
	private static $_before_instance,$_after_instance,$_lastinstance;

	public function add($route,array $stack,$atFirst=false){
		$handle['callback']=$stack;
		$handle['routingTyp']="middleware";

		$this->set($route,$handle,"",$atFirst);	//im stack sind alle middlewares drin die für diese route ausgeführt werden sollen
	}

	public function addStd(array $stack){
		$this->map['std']=$stack;
	}

	public static function middle($typ="") {
		if($typ=="before"){
			self::$_lastinstance=self::before();
			return self::before();
		}
		if($typ=="after"){
			self::$_lastinstance=self::after();
			return self::after();
		}

		return self::$_lastinstance;
	}

	public static function before(){
		if(!isset(self::$_before_instance)){
			self::$_before_instance=new self();
		}
		return self::$_before_instance;
	}

	public static function after(){
		if(!isset(self::$_after_instance)){
			self::$_after_instance=new self();
		}
		return self::$_after_instance;
	}
}