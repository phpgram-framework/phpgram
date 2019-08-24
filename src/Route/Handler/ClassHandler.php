<?php
/**
 * Created by PhpStorm.
 * User: joern
 * Date: 24.08.2019
 * Time: 16:26
 */

namespace Gram\Route\Handler;


class ClassHandler extends Handler
{
	protected $class,$function;

	public function callback(){
		return array(new $this->class,$this->function);
	}

	/**
	 * @param string $class
	 * @param string $function
	 * @throws \Exception
	 */
	public function set($class="",$function=""){
		if($class==="" || $function===""){
			throw new \Exception("Keine Klasse oder Funktion angegeben");
		}

		$this->class=$class;
		$this->function=$function;
	}

	public static function __set_state($vars){
		$handler = new self();
		$handler->class=$vars['class'];
		$handler->function=$vars['function'];

		return $handler;
	}
}