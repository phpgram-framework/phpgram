<?php
/**
 * phpgram
 *
 * This File is part of the phpgram Micro Framework
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE
 *
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\Callback;

use Gram\Middleware\Classes\ClassInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ClassHandler
 * @package Gram\Handler
 *
 * Erstellt einen Handler aus einer Klasse und Funktion
 */
class ClassCallback implements CallbackInterface
{
	protected $class,$function,$param;

	/** @var ServerRequestInterface */
	protected $request;

	/**
	 * @inheritdoc
	 *
	 * Baue das Callback zusammen und führe es aus
	 *
	 * Bestehend aus der Klasse und der Funktion
	 *
	 * @param array $param
	 * @param ServerRequestInterface $request
	 * @return mixed|string
	 *
	 * Gebe das fertige Callback zurück (als Array für call_user_function)
	 */
	public function callback($param=[],ServerRequestInterface $request)
	{
		$this->request = $request;
		$this->param = $param;

		$class = new $this->class;

		$this->tryToPsr($class);	//versuche Psr zu setzen

		$callback = [$class,$this->function];

		$return = call_user_func_array($callback,$this->param);

		$this->tryToPsr($class,true);	//versuche Psr zurück zu bekommen

		return ($return===null)?'':$return;	//default: immer einen String zurück geben
	}

	private function tryToPsr($class,$des=false)
	{
		if($class instanceof ClassInterface){
			if($des===false){
				$class->setPsr($this->request);
			}else{
				$this->request = $class->getRequest();
			}
		}else{
			if($des===false){
				$this->param[]=$this->request; //letzer param ist dann der request
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getRequest(): ServerRequestInterface
	{
		return $this->request;
	}

	/**
	 * @inheritdoc
	 *
	 * Nimmt Klasse und Funktion entgegen
	 *
	 * Speichert diese um sie für das Callback zusammen zubauen
	 *
	 * @param string $class
	 * @param string $function
	 * @throws \Exception
	 */
	public function set($class="",$function="")
	{
		if($class==="" || $function===""){
			throw new \Exception("Keine Klasse oder Funktion angegeben");
		}

		$this->class=$class;
		$this->function=$function;
	}
}