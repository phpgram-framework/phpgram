<?php
namespace Gram\Lib;

/**
 * Class CookieH
 * @package Gram\Lib
 * @author Jörn Heinemann
 * @version 1.0
 * Core class um Cookies zu verwalten
 * Es werden nur httponly Cookies verwendet
 */
class CookieH
{
	/**
	 * Setzt einen Cookie
	 * @param string $key
	 * Name des Cookies
	 * @param mixed $value
	 * Wert des Cookies
	 * @param int $expiry
	 * wann der Cookie ablaufen soll
	 * @return bool
	 */
	public static function set($key, $value,$expiry){
		return setcookie($key,$value,$expiry,"/",null,null,true);
	}

	/**
	 * Gibt einen Cookiewert zurück
	 * @param string $key
	 * Name des Cookies
	 * @return bool|mixed
	 */
	public static function get($key){
		if(!self::existK($key)){
			return false;
		}

		return $_COOKIE[$key];
	}

	/**
	 * prüft ob es diesen Cookie gibt
	 * @param string $key
	 * name des Cookies
	 * @return bool
	 */
	public static function existK($key){
		return isset($_COOKIE[$key]);
	}

	/**
	 * Prüft ob die Session gestartet wurde
	 * @deprecated useless
	 * @return bool
	 */
	public static function sessionStarted(){
		return empty($_SESSION);
	}

	/**
	 * Löscht einen Cookie
	 * @param string $key
	 * Name des zu löschenden Cookies
	 */
	public static function delete($key){
		self::set($key,"",time()-1);
	}
}