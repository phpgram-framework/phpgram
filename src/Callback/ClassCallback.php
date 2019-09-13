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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ClassHandler
 * @package Gram\Handler
 *
 * Erstellt einen Handler aus einer Klasse und Funktion
 */
class ClassCallback implements CallbackInterface
{
	protected $class,$function;

	/**
	 * Baue das Callback zusammen und führe es aus
	 *
	 * Bestehend aus der Klasse und der Funktion
	 *
	 * @param array $param
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return mixed|string
	 *
	 * Gebe das fertige Callback zurück (als Array für call_user_function)
	 */
	public function callback($param=[],ServerRequestInterface $request)
	{
		$callback = array(new $this->class,$this->function);
		$param[]=$request; //letzer param ist dann der request

		$return= call_user_func_array($callback,$param);

		return ($return===null)?'':$return;	//default: immer einen String zurück geben
	}

	/**
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