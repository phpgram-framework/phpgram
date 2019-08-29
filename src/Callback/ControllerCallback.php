<?php
namespace Gram\Callback;

/**
 * Class ControllerHandler
 * @package Gram\Handler
 * @author Jörn Heinemann
 * Konvertiert folgendes Muster: Controller@function zu class= Controller Function = function
 * Erstellt dann den Handler mit dem ClassHandler
 */
class ControllerCallback extends ClassCallback
{
	/**
	 * Nimmt einen Controller entgegen
	 * trennt den Controller in Klasse und Funktion
	 * Erstelle dann einen normalen ClassHandler
	 * @param string $controller
	 * @throws \Exception
	 */
	public function setC($controller=""){
		if($controller===""){
			throw new \Exception("Keinen Controller angegeben");
		}

		$extract = explode('@',$controller);

		parent::set($extract[0],$extract[1]);
	}
}