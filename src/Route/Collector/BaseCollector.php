<?php
namespace Gram\Route\Collector;
use Gram\Route\Interfaces\Collector;
use Gram\Route\Interfaces\Generator;
use Gram\Route\Interfaces\Components\DynamicGenerator;
use Gram\Route\Interfaces\Components\StaticGenerator;
use Gram\Route\Route;

/**
 * Class BaseCollector
 * @package Gram\Route\Collector
 * @author Jörn Heinemann
 * Fasst die Funktionen der Collectoren zusammen
 */
abstract class BaseCollector implements Collector
{
	protected $map=array(),$dynamicroutes=array(),$staticroutes=array(),$prefix='';

	private static $staticGenerator,$dynamicGenerator;

	/**
	 * Gibt alle verfügbaren Routes zurück
	 * @return array
	 */
	public function map(){
		return $this->map;
	}

	/**
	 * Sammelt erst alle Routes die geadded werden
	 * Wandelt die Userplaceholder in Regex um und zählt wie viele Varaiblen die Route parsen soll
	 * @param string $path
	 * Die Route von den Configseiten
	 * @param $handle
	 * Was bei einem Fund gemacht werden soll
	 * @param $method
	 * @param bool $atFirst
	 * Soll die Route ganz am Anfang des Route Arrays eingefügt werden
	 * z. B. für Middlewares die von einer Route hinzugefügt wurden (Middleware nur für eine bestimmte Route)
	 * diese würden sonst nicht matchen wenn andere Middlewares vorher stehen
	 * @return Route
	 */
	public function set($path,$handle,$method,$atFirst=false){
		$path=$this->prefix.$path;	//setze mögliches Gruppenprefix vorweg

		$route=new Route($path,$handle,$method);	//erstelle neue Route mit allen Parametern

		if(count($route->vars)===0){
			//Eine Staticroute
			if($atFirst){
				array_unshift($this->staticroutes,$route);
			}else{
				$this->staticroutes[]=$route;
			}
		}else{
			//Eine Route mit Parametern
			if($atFirst){
				array_unshift($this->dynamicroutes,$route);
			}else{
				$this->dynamicroutes[]=$route;
			}
		}

		return $route;
	}

	/**
	 * Erfasst eine Gruppe.
	 * Gruppen brauchen ein Prefix und ein Callback (eine Minimap mit Routes)
	 * Das derzeitige Prefix wird gespeichert und wird zu dem übergebenen
	 * Rufe das callback auf mit dem Sammler auf.
	 * Stelle dann bei jeder neu hinzugefügten Route des callbacks das Prefix voran
	 * Danach setze das Prefix wieder auf das alte
	 * Bei nested Groups wird diese Funktion rekursiv aufgerufen und somit erhalten die Routes immer das richtige prefix
	 * @param string $prefix
	 * Prefix das vor die Routes gestellt werden soll
	 * @param callable $callback
	 * Die Minimap mit den Sammlern
	 */
	public function setGroup($prefix,callable $callback){
		$currentprefix = $this->prefix;
		$this->prefix= $this->prefix.$prefix;

		call_user_func($callback);
		$this->prefix=$currentprefix;
	}

	/**
	 * Fasst die gesammelten Routes mit den Generatoren zusammen
	 */
	public function trigger(){
		$this->map=array_merge(
			$this->map,
			$this->generate(self::$staticGenerator,$this->staticroutes),
			$this->generate(self::$dynamicGenerator,$this->dynamicroutes)
		);
	}

	private function generate(Generator $generator,array $routes=array()){
		if(!empty($routes)){
			$routes= $generator->generate($routes);
		}

		return $routes;
	}

	public static function setGenerator(StaticGenerator $staticGenerator, DynamicGenerator $dynamicGenerator){
		self::$staticGenerator=$staticGenerator;
		self::$dynamicGenerator=$dynamicGenerator;
	}
}