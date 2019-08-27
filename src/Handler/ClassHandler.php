<?php
namespace Gram\Handler;

/**
 * Class ClassHandler
 * @package Gram\Handler
 * @author Jörn
 * Erstellt einen Handler aus einer Klasse und Funktion
 *
 */
class ClassHandler implements Handler
{
	protected $class,$function;

	/**
	 * Baue das Callback zusammen und führe es aus
	 * Bestehend aus der Klasse und der Funktion
	 * @param array $param
	 * @param $request
	 * @return array
	 * Gebe das fertige Callback zurück (als Array für call_user_function)
	 */
	public function callback($param=array(),$request){
		//prüfe ob die Klasse ein Controller ist, wenn ja gebe das Request Object an den Konstruktor
		if(!$this instanceof ControllerHandler){
			$callback = array(new $this->class,$this->function);
			$param[]=$request; //letzer param ist dann der request
		}else{
			$callback=array(new $this->class($request),$this->function);
		}

		return call_user_func_array($callback,$param);
	}

	/**
	 * Nimmt Klasse und Funktion entgegen
	 * Speichert diese um sie für das Callback zusammen zubauen
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
}