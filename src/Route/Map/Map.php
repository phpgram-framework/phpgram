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
	protected $map=array(),$options=array();

	/**
	 * Sucht die passende Map von Routes und Handlern
	 * Diese kann auch gecacht sein
	 * @return array
	 */
	public function getMap(){
		//wenn map noch nicht gesetzt wurde
		if(empty($this->map)){
			//prüfe ob es einen cache gibt und cache falls es keinen gibt
			if($this->options['caching']){
				$this->getCache();
			}else{
				$this->createMap();
			}
		}

		return $this->map;
	}

	/**
	 * Übertrage Optionen (ob gechacht werden soll und wo die Datei ist bzw. diese gespeichert werden soll
	 * @param $options
	 */
	protected function init($options){
		$this->options=$options;
	}

	/**
	 * Wenn gecachet werden darf, hole den Routes cache bzw. erstelle ihn
	 */
	protected function getCache(){
		$cache=$this->options['cache'];
		if(file_exists($cache)){
			$this->map=require $cache;
		}else{
			$this->createMap();
			//wenn gecacht werden soll es aber noch keinen cache gab: cache erstellen

			file_put_contents(
				$cache,
				'<?php return ' . var_export($this->map, true) . ';'
			);
		}
	}

	/**
	 * Methode wie die Map geladen bzw. erstellt werden soll
	 * @return mixed
	 */
	abstract protected function createMap();
}