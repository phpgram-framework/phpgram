<?php
namespace Gram\Lib;

/**
 * Class Autoloader
 * @package Gram\Lib
 * @author Jörn Heinemann
 * @version 1.0
 * @deprecated Composer benutzen
 * läd die Klassen gemäß psr-4
 */
class Autoloader
{
	private $namespaces=array(), $classmap=array(), $fastload, $prefix, $name, $class, $classfile;

	/**
	 * Registriert den Autoloader mit sql
	 * @param bool $fastload
	 * $fastload = true -> include Klasse ohne zu prüfen ob es die Datei gibt
	 */
	public function register($fastload){
		$this->fastload=$fastload;	//soll bei namespace geprüft werden ob File existiert (wenn nicht -> läd schneller)
		spl_autoload_register(array($this, 'load'));
	}

	/**
	 * Nimmt die Namespaces für psr-4 entgegen
	 * @param array $namespaces
	 */
	public function registerNameSpace(Array $namespaces){
		$this->namespaces=$namespaces;
	}

	/**
	 * Nimmt Ordnerpfade entgegen in denen auch nach Klassen gesucht werden soll
	 * wenn Klasse globalen Namespace hat
	 * @param array $classmap
	 */
	public function registerClassMap(Array $classmap){
		$this->classmap=$classmap;
	}

	/**
	 * Klassen Loader
	 * Läd eine Klasse anhand des übergebeben Namens
	 * Prüft zuerst ob die Klasse einen Namespace besitzt
	 * wenn ja lade die File anhand des Namespaces
	 * sonst suche die File
	 * @param string $name
	 * Klassenname
	 */
	public function load($name){
		$this->name=$name;
		$this->prefix=explode('\\',$this->name); //wandle Klassenname in Array um
		$this->class=array_pop($this->prefix);	 //eigentliche Klasse, schneidet classennamen raus

		if(count($this->prefix)==0){
			//Classmap
			$this->loadClassMap();
		}else{
			//Namespace
			$this->loadNameSpace();
		}
	}

	/**
	 * Suche File anhand eines übergebenen Klassennames
	 * verwendet für Klassen mit globalen Namespace
	 */
	private function loadClassMap(){
		foreach ($this->classmap as $path) {
			$this->classfile=$path.$this->name;	// zu suchende Datei
			//wenn klasse in classmap gefunden
			if($this->loadFile()){
				break;
			}
		}
	}

	/**
	 * Sucht File anhand eines übergeben Namespace
	 * verwendet für Klassen mit Namespace
	 */
	private function loadNameSpace(){
		$prefix=implode('\\',$this->prefix);	//setze Prefix ohne Klassennamen wieder zusammen um Namespace zu finden
		$this->classfile=$this->namespaces[$prefix].$this->class;	//suche Namespace
		$this->loadFile($this->fastload);
	}

	/**
	 * Läd die File entweder mit durch einen Namespace oder indem der Klassenname gefunden wurde
	 * @param bool $check
	 * Soll geprüft werden ob es File gibt oder gleich geladen werden
	 * @return bool
	 * true wenn die File geladen wurde
	 * sonst false
	 * benötigt für loadClassMap() in der Schleife
	 */
	private function loadFile($check=true){
		$classfile = $this->classfile.".php";
		if(!$check || file_exists($classfile)){
			require_once ($classfile);

			debug_console("Autoloader: Klasse $this->name geladen");
			return true;
		}
		return false;
	}
}