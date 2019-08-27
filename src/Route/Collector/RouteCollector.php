<?php
namespace Gram\Route\Collector;

/**
 * Class RouteCollector
 * @package Gram\Route\Collector
 * @author Jörn Heinemann
 * Erstellt die auf zurufenden Routes in einer Liste
 * Wird nur aufgerufen wenn kein Cache vorliegt
 */
class RouteCollector extends Collector
{
	private static $_instance;

	/**
	 * Fügt eine Webroute hinzu
	 * @param string $route
	 * Welche Route
	 * @param $controller
	 * Welcher Controller soll angesprochen werden
	 * @param string $method
	 * Wie soll die RouteCollector aufgerufen werden
	 * @return bool|\Gram\Route\Route
	 */
	public function add($route,$controller,$method='get'){
		$handle['callback']=$controller;
		$handle['routingTyp']="web";

		return $this->set($route,$handle,$method);
	}

	/**
	 * Fügt eine Api RouteCollector hinzu. Kaum ein unterschied zu add()
	 * @param $route
	 * @param $controller
	 * @param string $method
	 * @return bool|\Gram\Route\Route
	 */
	public function api($route,$controller,$method='get'){
		$handle['callback']=$controller;
		$handle['routingTyp']="api";

		return $this->set($route,$handle,$method);
	}

	/**
	 * Setzt die 404 Seite
	 * @param $controller
	 * @param string $function
	 */
	public function notFound($controller,$function=""){
		if($function===""){
			$this->map['er404']=$controller;
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
		$this->map['erNotAllowed']=$controller;

		if($function===""){
			$this->map['erNotAllowed']=$controller;
		}

		$this->map['erNotAllowed']=array($controller,$function);
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