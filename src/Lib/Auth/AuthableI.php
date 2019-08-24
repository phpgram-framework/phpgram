<?php
namespace Gram\Lib\Auth;

/**
 * Interface AuthableI
 * @package Gram\Lib\auth
 * @author Jörn Heinemann
 * @version 2.0
 * Core Interface mit Funktionen die die auth class benötigt
 * Müssen in das Usermodel eingebunden werden, da die Auth nicht weiß wie die Datenbank aufgebaut ist
 * Die Usertabelle muss folgende Columns enthalten: 'username':text, 'password':text
 * Es braucht eine extra Tabelle mit den Columns: 'userid':int, 'sessionid':text, 'token':text
 * zu der normalen Usetabelle.
 * Die Column namen können frei gewählt werden, müssen aber mit dem Usermodel verwaltet werden können
 */
interface AuthableI
{
	//getter
	/**
	 * Gebe Passwort des Users (gehasht) zurück
	 * @return mixed
	 */
	public function getPw();

	/**
	 * Gebe den Usernamen zurück
	 * @return mixed
	 */
	public function getUserName();

	/**
	 * Gebe Userid zurück
	 * @return mixed
	 */
	public function getUserId();

	//setter in die DB übertragen

	/**
	 * Update Passwort
	 * @param string $pwhash
	 * Passwort gehasht
	 * @return bool
	 * Gibt true bei Erfolg zurück
	 * oder false bei Misserfolg
	 */
	public function setPw($pwhash):bool;

	/**
	 * Fügt ein neuen Cookie hinzu
	 * selectUserBy.. Funktion muss vorher ausgeführt sein
	 * @param string $sessionid
	 * Die zu setzende Sessionid
	 * @param string $token
	 * Das zu setzende Token
	 * @return bool
	 * true bei Erfolg
	 * sonst false
	 */
	public function insertCookie($sessionid,$token):bool;

	/**
	 * Setzt eine Sessionid. Das ist die ID des Gerätes
	 * @param string $oldsessionid
	 * Zu Ändernde Token
	 * @param string $sessionid
	 * Die zu setzende Sessionid
	 * @return bool
	 * true bei Erfolg
	 * sonst false
	 */
	public function setSession($oldsessionid,$sessionid):bool;

	/**
	 * Setzt ein neues Token nach einem Cookielogin
	 * @param string $oldtoken
	 * zu Ändernde Token
	 * @param string $token
	 * Das zu setzende Token
	 * @return bool
	 * Gibt true bei Erfolg zurück
	 * sonst false
	 */
	public function setToken($oldtoken,$token):bool;

	/**
	 * @param int $userid
	 * Welcher user
	 * @param string $sessionid
	 * Welches Gerät
	 * @return bool
	 */
	public function deleteSession($userid,$sessionid):bool;

	/**
	 * Bearbeite Userinformationen
	 * @param string $nameold
	 * Alter Username um den User zu zurodnen
	 * @param string $name
	 * Neuer Username.
	 * $name = "" -> Username nicht verändern
	 * @param array $infos
	 * Weitere Userinformationen um diese in die Db zu speichern
	 * @return bool
	 * Gebe true bei Erfolg und false bei Misserolg zurück
	 */
	public function updateUser($nameold,$name,$infos=array()):bool;

	/**
	 * Füge neuen User hinzu
	 * @param string $username
	 * Der Username des Users
	 * Name muss eindeutig und nicht in der DB sein (wird von Auth geprüft)
	 * @param string $pw
	 * gehashtes Passwort
	 * @param array $infos
	 * Weitere Userinformationen die in die DB gespeichert werden sollen
	 * @return bool
	 * Gibt true bei Erfolg zurück
	 * oder false bei Misserfolg
	 */
	public function setuser($username,$pw,$infos=array()):bool;

	/**
	 * Lade user Informationen und prüfe ob es den User gibt
	 * Anhand des Usernamen
	 * @param string $name
	 * Username um den User zu laden
	 * @return bool
	 * Gebe true zurück wenn es den User gibt
	 * sonst false
	 */
	public function selectUserByName($name):bool;

	/**
	 * Lade User Informationen und prüfe ob es den User gibt
	 * Anhand des Token und der Session id
	 * für Cookie
	 * @param string $sessionid
	 * Session id um das Gerät zu identifizieren
	 * @param string $token
	 * Token um den User zu identifiezieren
	 * @return bool
	 * Gebe true zurück wenn es den User gibt
	 * sonst false
	 */
	public function selectUserByCookie($sessionid,$token):bool;

	/**
	 * Prüfe ob es den Usernamen schon gibt
	 * Für die Auswahl des Usernamens bei Register
	 * @param string $username
	 * Zu pürfender Username
	 * @return bool
	 * Gibt true zurück wenn Usernamen gefunden
	 * sonst false
	 */
	public function userExist($username):bool;

	/**
	 * Prüfe ob die Sessionid eindeutig ist
	 * @param string $sessionid
	 * zu Prüfende Sessionid
	 * @return bool
	 * true wenn es diese gibt
	 * sonst false
	 */
	public function sessionIDExist($sessionid):bool;

	/**
	 * Prüfe ob es das Token schon gibt
	 * Für Register
	 * @param string $token
	 * Zu prüfende Token
	 * @return bool
	 * Gibt true zurück wenn ein Token gefunden wurde
	 * sonst false
	 */
	public function tokenExist($token):bool;
}