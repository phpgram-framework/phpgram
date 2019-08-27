<?php
namespace Gram\Route\Map;

/**
 * Class Map
 * @package Gram\Route\Map
 * @author Jörn Heinemann
 * Die Abstracte Klasse von der alle Map Klassen erben müssen
 * Maps sind dazu da die verfügbaren Routes mit den Handlern zu holen und zu erstellen
 */
abstract class Map
{
	protected $map=array(),$options=array(),$cache=null;

	/**
	 * Sucht die passende Map von Routes und Handlern
	 * Diese kann auch gecacht sein
	 * @return array
	 */
	public function getMap(){
		//wenn map noch nicht gesetzt wurde
		if(empty($this->map)){
			//prüfe ob es einen cache gibt und cache falls es keinen gibt
			if(isset($this->cache)){
				$this->getCache();
			}else{
				$this->createMap();
			}
		}

		return $this->map;
	}

	/**
	 * Wenn gecachet werden darf, hole den Routes cache bzw. erstelle ihn
	 */
	protected function getCache(){
		if(file_exists($this->cache)){
			$this->map=require $this->cache;
		}else{
			$this->createMap();
			//wenn gecacht werden soll es aber noch keinen cache gab: cache erstellen

			file_put_contents(
				$this->cache,
				'<?php return ' . var_export($this->map, true) . ';'
			);
		}
	}

	/**
	 * Methode wie die Map geladen bzw. erstellt werden soll
	 * @return mixed
	 */
	abstract protected function createMap();

	/**
	 * Speichere alle Routesammler
	 * Diese werden dann ausgeführt wenn die Map aufgerufen wird
	 * @param callable $routes
	 * Eine Funktion mit Routesamllern drin
	 * @param string $type
	 * @param bool $caching
	 * Soll gecacht werden
	 * Optional
	 * @param string $cache
	 * Die File aus der der Cache geladen werden soll
	 * Optional
	 * @return
	 */
	abstract public static function map(callable $routes,$type="",$caching=false,$cache="");
}