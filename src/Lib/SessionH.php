<?php
namespace Gram\Lib;

/**
 * Class SessionH
 * @package Gram\Lib
 * @author Jörn Heinemann
 * @version 1.0
 * Core class um die Session zu verwalten
 * Die Session wird httponly gesetzt und für hijacking geschützt
 */
class SessionH
{
    private static $_sessionStarted=false;

	/**
	 * Startet eine Session wenn diese noch nicht gestartet wurde
	 * Prüft gleichzeitig auf hijacking
	 */
    private static function start(){
        if(!self::$_sessionStarted){
        	ini_set('session.cookie_httponly',true);
            session_start();
            self::$_sessionStarted=true;
        }

        //Um Session Hijacking zu verhindern. Bei jeder Session aktion wird geprüft ob die IP noch die selbe ist wie zu beginn der Sesiion
        if(!isset($_SESSION['last_ip']) && self::$_sessionStarted)
			$_SESSION['last_ip'] = $_SERVER['REMOTE_ADDR'];

		if($_SESSION['last_ip']!=$_SERVER['REMOTE_ADDR'])
        	self::destroy();
    }

	/**
	 * Setzt einen Wert in die Session
	 * Funktioniert auch mit Arrays
	 * @param string $key
	 * Index in der Session
	 * @param mixed $value
	 * Wert der eingesetzt werden soll
	 */
	public static function set($key, $value){
        self::start();

        //wenn das array bereits daten hat nur daten austauschen die neu sind
        if(isset($_SESSION[$key]) && is_array($value)){
			foreach ($value as $key1=>$item){
				if(self::get($key,$key1)!=$value[$key1]){
					$_SESSION[$key][$key1]=$item;
				}
			}
		}else
			$_SESSION[$key]=$value;
    }

	/**
	 * Holt einen Wert aus der Session
	 * Funktioniert auch mit ein dim Arrays
	 * @param string $key
	 * Index in der Session
	 * @param bool|string $key2
	 * Wenn gesetzt der Index im Array
	 * @return bool|mixed
	 * Gibt Wert zurück oder
	 * false wenn der Wert nciht gefunden wurde
	 */
	public static function get($key, $key2=false){
        self::start();

        //2 Dim Array
        if($key2!=false && isset($_SESSION[$key][$key2]))
            return $_SESSION[$key][$key2];
        elseif ($key2!=false){
        	return false;
		}

        return (isset($_SESSION[$key]))?$_SESSION[$key]:false;
    }

	/**
	 * Prüft ob es einen Index gibt
	 * @param string $key
	 * Index in der Session
	 * @param bool|string $key2
	 * wenn gesetzt Index des Arrays
	 * @return bool
	 * gibt true zurück wenn Wert gefunden
	 * sonst false
	 */
    public static function existK($key,$key2=false){
		self::start();

		return $key2?(isset($_SESSION[$key][$key2])):(isset($_SESSION[$key]));
	}

	/**
	 * Prüft ob die Session gestart wurde
	 * @deprecated wird in den Sessionfunktionen verwaltet
	 * @return bool
	 */
    public static function sessionStarted(){
        return empty($_SESSION);
    }

	/**
	 * Löscht und löst die Session auf
	 */
    public static function destroy(){
		self::start();

    	if(self::$_sessionStarted){
			session_unset();
			session_destroy();
			self::$_sessionStarted=false;
		}
    }
}