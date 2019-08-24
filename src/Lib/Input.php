<?php
namespace Gram\Lib;

/**
 * Class Input
 * @package Gram\Lib
 * @author Jörn Heinemann
 * @version 1.0
 * Core class um Inputs von Post oder Get zu verwalten
 * bietet auch Funktionen an um Inputs von xss Möglichkeiten zu säubern und auf Vollständigkeit zu prüfen
 */
class Input
{
	private $strict, $value, $name, $check;

	//Input get

	/**
	 * holt sich einen Inputwert (get oder post) und filtert alle xss Möglichkeiten raus
	 * funktioniert auch bei Arrays
	 * funktioniert auch mit meheren Inputindizes auf einmal
	 */
	private function getInput(){
		//wenn mehere Inputs in einem Array zusammengefasst werden sollen
		if(is_array($this->name)){
			foreach ($this->name as $key=>$item) {
				//speichere den Input an der gleichen Stelle wie der name des Inputs
				$this->value[$key]=$this->post_get($item);
			}
		}else{
			$this->value=$this->post_get($this->name);
		}

		$this->cleanInput();
	}

	/**
	 * Gibt einen Wert zurück der mit get oder post übergeben wurde
	 * @param $name
	 * @return string
	 */
	private function post_get($name){
		if(isset($_POST[$name])){
			return $_POST[$name];
		}elseif(isset($_GET[$name])){
			return $_GET[$name];
		}else{
			return "";
		}
	}

	//Input säubern

	/**
	 * Filtert alle Xss Möglichkeiten raus
	 * funktioniert auch über Arrays
	 */
	private function cleanInput(){
		if(is_array($this->value)){
			array_walk_recursive($this->value,array($this, 'cleanInputArray'));
			return;
		}

		$this->value = $this->cleanInputSingle($this->value);
	}

	/**
	 * Bekommt Wert von einem Array übergeben und wendet die Filterfunktion an
	 * @param $a
	 */
	private function cleanInputArray(&$a){
		$a = $this->cleanInputSingle($a);
	}

	/**
	 * Wendet die Filterfunktion auf einen Wert an
	 * @param $a
	 * @return string
	 */
	private function cleanInputSingle($a){
		return htmlspecialchars($a,ENT_QUOTES);
	}

	//Input auf vollständigkeit prüfen

	/**
	 * Prüft ob Inputwerte vollständig ausgefüllt sind
	 */
	private function checkInput(){
		if(is_array($this->value)){
			array_walk_recursive($this->value,array($this, 'checkArray'));
			return;
		}

		$this->check = $this->checkSingle($this->value);
	}

	/**
	 * Wendet die Prüffunktion für jeden Wert des Arrays an
	 * Wenn strict = false: gebe true zurück sobald ein Wert gesetzt wurde
	 * sonst prüfe jeden Wert ob dieser gesetzt ist
	 * @param $a
	 */
	private function checkArray(&$a){
		if($this->strict){
			//wenn alle werte gesetzt sein müssen
			if(!isset($this->check) || $this->check==true){
				$this->check=$this->checkSingle($a);
			}else{
				return;
			}
		}else{
			//wenn nur ein wert gesetzt sein muss
			if(!isset($this->check) || $this->check==false){
				//wenn noch kein wert positiv geprüft wurde
				$this->check=$this->checkSingle($a);
			}else{
				return;
			}
		}
	}

	/**
	 * Überprüft einen einzelnen Wert ob dieser gesetzt wurde
	 * @param $a
	 * @return bool
	 */
	private function checkSingle($a){
		if(isset($a) && $a!="" && $a!=null){
			return true;
		}

		return false;
	}

	/**
	 * Gibt einen Input, Array (post oder get) zurück der auch gleich gefiltert wird
	 * Es ist auch möglich mehre Inputwerte per Array zu bekommen
	 * bsp.: $input = Input::get('test'); gibt Value von test zurück
	 * bsp.: $input = Input::get(array('testinput'=>'test')); hier wird die Value von test am Index testinput gespeichert
	 * @param string $name
	 * Index des Arrays oder Array mit Indizes
	 * @return mixed
	 */
	public static function get($name){
		$input=new Input();
		$input->name=$name;

		$input->getInput();

		return $input->value;
	}

	/**
	 * Gibt einen gefilterten Wert oder Array zurück
	 * @param mixed $value
	 * @return mixed
	 */
	public static function clean($value){
		$input=new Input();
		$input->value=$value;;

		$input->cleanInput();

		return $input->value;
	}

	/**
	 * Prüft ob das ein Wert oder ein Array vollständig ist
	 * @param mixed $value
	 * @param bool $strict
	 * Wenn gesetzt muss jeder Wert gesetzt sein
	 * @return bool
	 */
	public static function check($value,$strict=true){
		$input=new Input();
		$input->value=$value;
		$input->strict=$strict;

		$input->checkInput();

		if($input->check==null){
			return false;
		}
		return $input->check;
	}

	/**
	 * Fasst die kernfunktionen zusammen:
	 * Holt sich einen Inputwert oder ein Array, filtert dieses und überprüft auf Vollständigkeit
	 * @param string|array $name
	 * Index bzw. Array mit Indizes
	 * @param bool $strict
	 * Wenn es ein Array ist soll jeder Wert überprüft werden
	 * @return bool
	 * false wenn Werte nicht vollständig sind
	 * Gefiltertes Array bzw Wert der auch auf Vollständigkeit geprüft wurde
	 */
	public static function gAC($name,$strict=true){
		$input=new Input();
		$input->name=$name;
		$input->strict=$strict;

		$input->getInput();
		$input->checkInput();

		if($input->check!=null && $input->check==true){
			return $input->value;
		}
		return false;
	}
}