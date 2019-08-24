<?php
namespace Gram\Lib;


class Middleware
{
	private static $_instance,$middlewares;

	public static function registerMiddle(Array $handle){
		if(!isset($handle['c']) || !isset($handle['f']) || !isset($handle['name']) || !isset($handle['kind'])){
			return;
		}

		array_push(self::$middlewares,$handle);
	}

	public static function getInstance() {
		return new Middleware;
	}

	public static function route() {
		if(!isset(self::$_instance)) {
			self::$_instance = self::getInstance();
		}
		return self::$_instance;
	}


}