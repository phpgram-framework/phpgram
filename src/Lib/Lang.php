<?php
namespace Gram\Lib;

/**
 * Class Lang
 * @package Gram\Lib
 * @author Jörn Heinemann
 * @version 2.4
 * Core class zum verwalten von Sprachen
 * es können beliebig viele Sprachen im gleichen Format angezeigt werden
 */
class Lang
{
	private static $_instance, $lang, $side, $langs=array();

	/**
	 * Läd die Sprachdatei
	 */
    private function getLangData(){
    	self::$langs=loadJSON(GRAMCONFIG."text".DS.self::$lang.DS.self::$side.".json");
	}

	/**
	 * Gibt die aktuel ausgewählte Sprache zurück
	 * Wenn keine gefunden wurde wird die Standardsprache gewählt
	 * @return bool|mixed
	 * Gibt die Sprache zurück
	 * oder false wenn es keine gibt
	 */
	public function islang(){
		$lang=SessionH::get('user','lang');

		//wenn sprache nicht gesetzt setzte std sprache in die session
		if(!$lang){

			$lang=CookieH::get('lang');
			if(!$lang){
				$lang=$this->getStdLang();
			}

			SessionH::set("user",array(
				"lang"=>$lang
			));
		}

		return $lang;
	}

	/**
	 * Ändert die aktuelle Sprache zur übergebenen Sprache
	 * Setzt die neue auch in den Cookie
	 * @param mixed $lang
	 */
	public static function changeLang($lang){
    	//session änderung
		SessionH::set("user",array(
			"lang"=>$lang
		));

		//cookie änderung
		CookieH::set('lang',$lang,STDCOOKIEEXP);
	}

	/**
	 * Gibt die Standardsprache zurück aus der configdatei
	 * @return mixed|string
	 */
	public function getStdLang(){
    	$langs=loadJSON(GRAMCONFIG."text/suplangs.json");
    	return $langs["stdlang"];
	}

	/**
	 * Gibt alle in der Configdatei enthaltenen Sprachen zurück
	 * @return mixed|array
	 */
	public function getAvaLang(){
    	$langs=loadJSON(GRAMCONFIG."text/suplangs.json");
    	unset($langs["stdlang"]);	//Index der Standardsprache wird hier nicht gebraucht
		return $langs;
	}

	/**
	 * Identifizert eine Sprache
	 * Gibt entweder den Namen der Sprache oder die Sprachid zurück
	 * @param int $id
	 * Der Name der Sprache u der die Id passt
	 * @param bool|string $q
	 * Die Id zu dem der Name passt
	 * @return bool|int|string
	 */
	public function getLangById($id,$q=false){
		$langs=loadJSON(GRAMCONFIG."text/suplangs.json");

		//Gibt namen der Sprache zurück
		if(!$q){
			return $langs[$id];
		}

		//Sucht zu einem gegebenen Namen die Sprachid
		$key=array_search($id,$langs);

		if(is_bool($key)){
			return false;
		}

		return $key;
	}

	/**
	 * Gibt ein neues Objekt zurück
	 * @return Lang
	 */
	public static function lang() {
		if(!isset(self::$_instance)) {
			self::$_instance = new Lang();
		}
		return self::$_instance;
	}

    /**
     * Übergibt den richtigen Sprachstring
     * @param $side
	 * auf welcher Seite soll der String angezeigt werden
     * @param $part
	 * welcher Teil der Seite steht der String
     * @return mixed|string
     */
    public static function getLang($side,$part){
		$lang = self::lang()->islang();	//Ausgewählte Sprache

        //wenn noch keine instance erzeugt bzw sprache geändert wurde
        if(!isset(self::$lang) || self::$side!=$side || self::$lang!=$lang || !isset(self::$langs)){
            self::$lang=$lang;
			self::$side=$side;
            self::lang()->getLangData(); //Sprachdatei
        }

        return self::$langs[$part];
    }
}