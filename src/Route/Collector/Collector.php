<?php
namespace Gram\Route\Collector;
use Gram\Route\Generator\DynamicGenerator;
use Gram\Route\Generator\StaticGenerator;
use Gram\Route\Route;

abstract class Collector
{
	protected $map=array(),$dynamicroutes=array(),$staticroutes=array(),$prefix='';

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
	 * @return Route
	 */
	protected function set($path,$handle,$method,$atFirst=false){
		$path=$this->prefix.$path;	//setze mögliches Gruppenprefix vorweg

		$route=new Route($path,$handle,$method);	//erstelle neue Route mit allen Parametern

		if($route->varcount===0){
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
		$static=array();
		$dynamic=array();

		if(!empty($this->staticroutes)){
			$staticgenerator = new StaticGenerator();
			$static=$staticgenerator->generate($this->staticroutes);
		}
		if(!empty($this->dynamicroutes)){
			$dynamicgenerator=new DynamicGenerator();
			$dynamic=$dynamicgenerator->generate($this->dynamicroutes);
		}

		$this->map=array_merge(
			$this->map,
			$static,
			$dynamic
		);
	}
}