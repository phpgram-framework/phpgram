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

use Gram\Middleware\Controller;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ControllerHandler
 * @package Gram\Handler
 *
 * Konvertiert folgendes Muster: Controller@function zu class= Controller Function = function
 *
 * Erstellt dann den Handler mit dem ClassHandler
 */
class ControllerCallback extends ClassCallback
{
	public function callback($param=[],ServerRequestInterface $request)
	{
		$class = new $this->class;

		if ($class instanceof Controller){
			$class->setPsr($request);	//gebe den Controllern die Psr Objekte
			$callback=array($class,$this->function);

			$return= call_user_func_array($callback,$param);

			return ($return===null)?'':$return;	//default: immer einen String zurück geben
		}

		return parent::callback($param,$request);
	}

	/**
	 * Nimmt einen Controller entgegen
	 *
	 * trennt den Controller in Klasse und Funktion
	 *
	 * Erstelle dann einen normalen ClassHandler
	 *
	 * @param string $controller
	 * @throws \Exception
	 */
	public function setC($controller="")
	{
		if($controller===""){
			throw new \Exception("Keinen Controller angegeben");
		}

		$extract = explode('@',$controller);

		parent::set($extract[0],$extract[1]);
	}
}