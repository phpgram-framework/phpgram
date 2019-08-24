<?php
namespace Gram\Lib\Auth;

use Gram\Lib\CookieH;
use Gram\Lib\Lang;
use App\Model\User;
use Gram\Lib\SessionH;

/**
 * Class Auth
 * @package Gram\Lib\auth
 * @author Jörn Heinemann
 * @version 2.0
 * Core class um einen Nutzer zu authentifizieren,
 * anzumelden oder Registrieren bzw. Verwalten
 */
class Auth
{
	//Password Hash:
	private $algo=PASSWORD_DEFAULT;
	private $cost=array('cost'=>12);

	private $user=null, $token, $session;

	/**
	 * Auth constructor.
	 * Bereitet die user Klasse vor. Prüft ob die Klasse User das Interface implementiert hat
	 * @param User|null $user
	 */
	public function __construct(User $user=null){
		$interfaces = class_implements('App\Model\User');
		//prüfe ob übergebene Klasse User auch alle nötigen Funktionen hat
		if (!isset($interfaces['Gram\Lib\Auth\AuthableI']) && !isset($interfaces['Gram\Lib\Auth\AuthableI_PHP_5'])) {
			return;
		}

		//Wenn kein Model übergeben wurde neues erstellen, sonst vorhandenes Model benutzen (zwecks DI)
		if($user==null){
			$this->user= new User();
		}else{
			$this->user= $user;
		}
	}

	/**
	 * Gibt das userobjekt zurück.
	 * je nach Aufruf ist das bereits mit Informationen gefüllt
	 * Dann können auch andere klassen auf diese Infos zugreifen
	 * @return User|null
	 */
	public function getUser(){
		return $this->user;
	}

	/**
	 * Führt den loginprozess durch.
	 * Dabei wird aber nur der username zum authentifizieren in der Session gespeichert,
	 * andere Werte mussen manuel gespeichert werden.
	 * Es wird hier auch ein Cookie gesetzt wenn es gewünscht ist.
	 * @param string $username
	 * @param string $password
	 * @param bool $cookie
	 * wenn gesetzt Cookie speichern
	 * @return bool
	 */
	public function login($username,$password,$cookie=false){
		//Prüfe ob User existiert und hole gleichzeitig die unterinfos
		if(!$this->checkUser($username) || !$this->pwValid($password)){
			return false;
		}

		$lang =Lang::lang()->islang();

		//Session
		SessionH::set('user',array(
			'username'=>$this->user->getUserName(),
			'userid'=>$this->user->getUserId()
		));

		//cookie
		if($cookie){
			if(!$this->generateCookie(true)){
				return false;
			}

			return CookieH::set('lang',$lang,STDCOOKIEEXP);
		}

		return true;
	}

	/**
	 * Löscht alle Userinfos aus der Session und alle Cookies
	 * @return bool
	 */
	public function logout(){
		$userid= SessionH::get('user','userid');

		SessionH::destroy();

		//Werte aus DB löschen
		$this->user->deleteSession($userid,CookieH::get('sessionid'));

		//Cookie löschen
		CookieH::delete('sessionid');
		CookieH::delete('token');

		return (!SessionH::existK('user','username'));
	}

	/**
	 * Prüft ob eingelogt ist.
	 * Pfüt ob es in der Session einen usernamen gibt,
	 * wenn nicht prüft ob es einen Cookie gibt und gleiche den mit der db ab,
	 * bei true hole die userinfos und setze diese in die session
	 * @return int
	 * = 1 wenn Session existieren
	 * = 0 wenn es fehlgeschlagen ist bzw. nicht eingelogt
	 * = 2 wenn mit einen cookie angemeldet wurde (um ggf. andere userinfos in die session zu speichern)
	 */
	public function isLoggedIn(){
		//wenn in session gesetzt
		if(SessionH::existK('user','username') && SessionH::existK('user','userid')) {
			return 1;
		}

		//auf cookie prüfen
		$seesionid = CookieH::get('sessionid');
		$token = CookieH::get('token');

		if(!$token || !$seesionid){
			return 0;
		}

		//hole user infos und prüfe gleichzeitig ob es den user mit dem gerät und dem token gibt
		if(!$this->user->selectUserByCookie($seesionid,$token)){
			return 0;
		}

		//erstellt nach jedem Cookie login ein neues Token und session id

		//Speichere neuen Cookie
		if(!$this->generateCookie()){
			return 0;
		}

		//setze Werte in die Session
		SessionH::set('user',array(
			'username'=>$this->user->getUserName(),
			'userid'=>$this->user->getUserId()
		));

		return 2;
	}

	/**
	 * Speichert einen User mithilfe des Usermodels ab
	 * @param string $name
	 * username. Muss nur einmal vorkommen.
	 * @param string $password
	 * Wunschpasswort das dann gehasht in die DB gespeichert wird
	 * @param array $infos
	 * weitere userinfos die ebenfalls in die DB gespeichert werden sollen
	 * Muss dann im Model verwaltet werden
	 * @param bool $check
	 * Wenn die Prüfung ob es den user schon gibt bereits an anderer Stelle durchgeführt wurde
	 * @return bool
	 */
	public function register($name,$password,$infos=array(),$check=false){
		//wenn es den user schon gibt
		if(!$check){
			if($this->user->userExist($name)){
				return false;
			}
		}

		//userdaten setzen
		return $this->user->setuser($name,$this->pwHash($password),$infos);
	}

	/**
	 * Aktualisiert die Nutzerinformationen
	 * setzt ggf. neuen Benutzernamen
	 * @param string $nameold
	 * Alter Nutzername. Wird geprüft ob es diesen gibt
	 * @param string $name
	 * Neuer Benutzername muss nicht gesetzt sein:
	 * prüfe ob es diesen schon gibt
	 * @param array $infos
	 * weitere zu ändernde Informationen
	 * werden im Model geändert
	 * @return bool
	 */
	public function updateUser($nameold,$name="",$infos=array()){
		if(!$this->checkUser($nameold)){
			return false;
		}

		//wenn username geändert werden soll es diesen aber schon gibt
		if($name!="" && $this->user->userExist($name)){
			return false;
		}

		return $this->user->updateUser($nameold,$name,$infos);
	}

	/**
	 * Ändert das Passwort und hasht es neu
	 * @param string $name
	 * Nutzername bei dem das pw geändert werden soll
	 * @param string $pwold
	 * altes Passwort zur überprüfung. das kann nur hier gemacht werden da nur hier der hash bekannt ist
	 * @param string $pwnew
	 * neues Passwort das dann gehasht ans Model übergeben wird
	 * @return bool
	 */
	public function changePW($name,$pwold,$pwnew){
		if(!$this->checkUser($name) || !$this->pwValid($pwold)){
			return false;
		}

		return $this->user->setPw($this->pwHash($pwnew));
	}

	/**
	 * Setzt ein neues Passwort ohne das alte zu kennen
	 * @param string $name
	 * Der Username
	 * @param string $pwnew
	 * Neue Passwort, dass gehasht wird
	 * @return bool
	 */
	public function resetPW($name,$pwnew){
		if(!$this->checkUser($name)){
			return false;
		}

		return $this->user->setPw($this->pwHash($pwnew));
	}

	//Password Hash

	/**
	 * Hasht ein Passwort mit dem geg. Algorithmus
	 * @param string $pw
	 * Das zu hashende Passwort
	 * @return bool|string
	 * Passworthash
	 */
	private function pwHash($pw){
		return password_hash($pw,$this->algo,$this->cost);
	}

	/**
	 * Prüft ob ein übergebenes Passwort mit dem hashten übereinstimmt
	 * @param string $pw
	 * Übergebene Passwort
	 * @return bool
	 */
	private function pwValid($pw){
		return password_verify($pw,$this->user->getPw());
	}

	/**
	 * Prüft ob userinformationen schon an anderer Stelle gesetzt wurden
	 * wenn nicht dann setze userinformationen und prüfe ob es den User gibt
	 * @param string $username
	 * Der zu prüfende Username
	 * @return bool
	 * true wenn es den user gibt
	 * sonst false
	 */
	private function checkUser($username){
		//wenn userinfos bereits an anderer Stelle gewfüllt wurden
		$checkname=$this->user->getUserName();
		if(isset($checkname)){
			return true;
		}
		return $this->user->selectUserByName($username);
	}

	/**
	 * Erstellt einen neuen Cookie oder Update einen bestehenden
	 * @param bool $new
	 * $new = true -> neuen Cookie erstellen
	 * $new = false -> bestehenden neu setzen
	 * @return bool
	 */
	private function generateCookie($new=false){
		$this->userSessionId();
		$this->userToken();

		//wenn user zum ersten mal einen Cookie setzt

		if($new){
			//DB Eintrag
			if(!$this->user->insertCookie($this->session,$this->token)){
				return false;
			}
		}else{
			$oldsid=CookieH::get('sessionid');
			$oldtoken = CookieH::get('token');

			//DB Eintrag
			if(!$this->user->setSession($oldsid,$this->session) || !$this->user->setToken($oldtoken,$this->token)){
				return false;
			}
		}

		return (CookieH::set('token',$this->token,STDCOOKIEEXP)
			&& CookieH::set('sessionid',$this->session,STDCOOKIEEXP));
	}

	/**
	 * Erstellt eine eindeutige Sessionid für den Cookie
	 * @return bool
	 */
	private function userSessionId(){
		//erstelle sessionid
		do{
			$this->session=self::generateToken(false);
		}while($this->user->sessionIDExist($this->session));	//wiederhole solange bis ein neues Token gefunden wurde

		return true;
	}

	/**
	 * Erstellt ein eindeutiges Token um den User mit Cookie an zumelden
	 * @return bool
	 */
	private function userToken(){
		//erstelle token
		do{
			$this->token=self::generateToken(false);
		}while($this->user->tokenExist($this->token));	//wiederhole solange bis ein neues Token gefunden wurde

		return true;
	}

	/**
	 * Erstellt ein Token und gebe es zurück sowie speichere es in der Session.
	 * Wenn ein Formular abgeschickt wurde kann überprüft werden ob das Token des Forms das gleiche ist wie das in der Session.
	 * um CSRF zu verhindern (unerlaubtes Formabsenden)
	 * @param bool $session
	 * Soll das Token in die Session gespeichert werden
	 * @return bool|string
	 * Gibt Token zurück
	 * oder false bei Misserfolg
	 */
	public static function generateToken($session=true){
		try {
			$token = bin2hex(random_bytes(10));
		} catch (\Exception $e) {
			echo $e;
			return false;
		}

		//setze Token in die Session um es beim form absenden zu überprüfen
		if($session){
			SessionH::set('token',$token);
		}

		return $token;		//gebe Token zurück um es ins form ein zubinden
	}

	/**
	 * Gleiche das übergebene Token mit dem aus der Session ab
	 * @param string $token
	 * Das zu Prüfende Token
	 * @return bool
	 */
	public static function validToken($token){
		return (SessionH::get('token') == $token);
	}
}