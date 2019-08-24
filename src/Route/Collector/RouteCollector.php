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

	//TODO ändern zu: Parameter für MiddlewareCollector. In handle müssen dann der name der middleware und die Params sein
	public function handle(Array $handle){

	}

	//TODO: Eine weitere Funktion die eine MiddlewareCollector hinzufügt vom Routing. Oder die bei einem übergebenen namen eine middleware überschreibt für diesen request

	/**
	 * Fügt eine Webroute hinzu
	 * @param string $route
	 * Welche Route
	 * @param string $controller
	 * Welcher Controller soll angesprochen werden
	 * @param string $function
	 * Welche Funktion im Controller
	 * @param string $method
	 * Wie soll die RouteCollector aufgerufen werden
	 * @return RouteCollector
	 * Gibt bestehendes Objekt zurück (um der RouteCollector noch mehr Eigenschaften hinzu zufügen)
	 */
	public static function add($route,$controller,$function,$method='get'){
		//baut den handler zusammen
		$handle['m']=$method;
		$handle['rm']="web";
		$callback=self::route()->createCallbackForMVC(CNAMESPACE.$controller,$function);

		if(!$callback){
			return self::route();
		}

		$handle['callback']=$callback;

		self::route()->set($route,$handle);

		return self::route();
	}

	/**
	 * Fügt eine Api RouteCollector hinzu. Kaum ein unterschied zu add()
	 * @param $route
	 * @param $controller
	 * @param $function
	 * @param string $method
	 * @return RouteCollector
	 */
	public static function api($route,$controller,$function,$method='get'){
		//baut den handler zusammen
		$handle['m']=$method;
		$handle['rm']="api";
		$callback=self::route()->createCallbackForMVC(CNAMESPACE.$controller,$function);

		if(!$callback){
			return self::route();
		}

		$handle['callback']=$callback;

		self::route()->set($route,$handle);

		return self::route();
	}

	public static function addFunc($route,callable $callable,$method='get'){
		//baut den handler zusammen
		$handle['m']=$method;
		$handle['rm']="api";

		$callback=self::route()->createCallbackFromCallable($callable);

		if(!$callback){
			return self::route();
		}

		$handle['callback']=$callback;

		self::route()->set($route,$handle);

		return self::route();
	}

	/**
	 * Setzt die 404 Seite
	 * @param $controller
	 * @param $function
	 */
	public static function notFound($controller,$function){
		$callback=self::route()->createCallbackForMVC(CNAMESPACE.$controller,$function);

		if(!$callback){
			return;
		}

		self::route()->map['er404']=$callback;
	}

	/**
	 * Setze die Method ist not allowed Seite
	 * Diese wird aufgerufen wenn die Route mit der falschen Methode z. B. post anstatt get aufgerufen wird
	 * @param $controller
	 * @param $function
	 */
	public static function notAllowed($controller,$function){

		$callback=self::route()->createCallbackForMVC(CNAMESPACE.$controller,$function);

		if(!$callback){
			return;
		}

		self::route()->map['erNotAllowed']=$callback;
	}

	public static function getInstance() {
		return new RouteCollector;
	}

	/**
	 * Gibt das aktuelle Objekt zurück
	 * @param bool $start
	 * Wenn der Router startet: fasse die Routes zu den Chunks zusammen
	 * @param array $paths
	 * Beim ersten Aufruf gebe an aus welchen Cofig Dateien die Routes geholt werden sollen
	 * @param array $placeholders
	 * Beim ersten Aufruf definiere die Custom Placeholder
	 * @return RouteCollector
	 */
	public static function route($start=false,$paths=array(),$placeholders=array()) {
		if(!isset(self::$_instance)) {
			self::$_instance = self::getInstance();

			self::$userplaceholders=$placeholders;

			if(!empty($paths)){
				//Hole die Routes aus den Config Dateien
				foreach ($paths as $path) {
					if (file_exists($path)) {
						include_once($path);
					}
				}
			}
		}

		//wenn gestartet gruppiere alle Routes
		if($start){
			self::$_instance->trigger();
		}

		return self::$_instance;
	}
}