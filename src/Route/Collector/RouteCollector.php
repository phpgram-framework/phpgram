<?php
namespace Gram\Route\Collector;

/**
 * Class RouteCollector
 * @package Gram\Route\Collector
 * @author Jörn Heinemann
 * Erstellt die auf zurufenden Routes in einer Liste
 * Wird nur aufgerufen wenn kein Cache vorliegt
 */
class RouteCollector extends BaseCollector implements \Gram\Route\Interfaces\Components\RouteCollector
{
	private static $_instance;

	/**
	 * Fügt eine Route hinzu
	 * @param string $route
	 * Welche Route
	 * @param $controller
	 * Welcher Controller soll angesprochen werden
	 * @param string $method
	 * Wie soll die RouteCollector aufgerufen werden
	 * @param $routingTyp
	 * Später können dann Outputstrategien für diesen Typ fest gelegt werden
	 * z. B. typ = 'web' -> content ausgeben oder typ = 'api' -> als json return
	 * @return bool|\Gram\Route\Route
	 */
	public function add($route,$controller,$routingTyp,$method='get'){
		$handle['callback']=$controller;
		$handle['routingTyp']=$routingTyp;	//speichere Routingtyp für (Output) Strategie

		return $this->set($route,$handle,$method);
	}

	/**
	 * Setzt die 404 Seite
	 * @param $controller
	 * @param string $function
	 */
	public function notFound($controller,$function=""){
		if($function==""){
			$this->map['er404']=$controller;
			return;
		}

		$this->map['er404']=array($controller,$function);
	}

	/**
	 * Setze die Method ist not allowed Seite
	 * Diese wird aufgerufen wenn die Route mit der falschen Methode z. B. post anstatt get aufgerufen wird
	 * @param $controller
	 * @param $function
	 */
	public function notAllowed($controller,$function=""){
		if($function===""){
			$this->map['er405']=$controller;
			return;
		}

		$this->map['er405']=array($controller,$function);
	}

	/**
	 * Gibt das aktuelle Objekt zurück
	 */
	public static function route() {
		if(!isset(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}