<?php
namespace Gram\Lib;
use \PDO;
use \PDOException;

/**
 * Class DB
 * @package Gram\Lib
 * @author Jörn Heinemann
 * @version 2.0
 * Core class um Datenbank Queries durch zuführen
 */
class DB
{
    private static $_instance;
    private $pdoHandle;

	/**
	 * DB constructor.
	 * Erstellt neues PDO Objekt mithilfe der Einträge aus der mvc.config.php
	 */
    private function __construct() {
        $this->pdoHandle = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME.';charset='.DBCHARSET.'', ''.DBUSER.'', ''.DBUSERPW.'');
    	$this->pdoHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

	/**
	 * Erstellt ein neues Datenbankobjekt
	 * @return DB
	 */
    public static function getPDOHandler() {
        return new DB();
    }

	/**
	 * gibt das PDOobjekt wieder
	 * spezielle Queries aus zuführen
	 * @return PDO
	 */
    public function get_PDO(){
        return $this->pdoHandle;
    }

	/**
	 * Führt ein Query mit parametrizierten Werten aus
	 * @param $sql
	 * Queryanweisung
	 * @param array $args
	 * Argumente, die dann gebunden werden
	 * @param int $fetch
	 * $fetch = 0 -> nur true oder false zurück geben
	 * $fetch = 1 -> nur den ersten Datensatz zurück geben (Benutzung bei eindeutigen Datensätzen)
	 * $fetch = 2 -> gibt alle gefunden Datensätze zurück
	 * @param null $fetchstyle
	 * wie das Rückgabearray aussehen soll
	 * @return array|bool|mixed
	 * Gibt true oder false zurück bzw. den / die Datensätze
	 */
    public function q($sql, $args=array(),$fetch=0,$fetchstyle=null){
    	try{
			$stmt = $this->pdoHandle->prepare($sql);
			$exce=$stmt->execute($args);

			if($fetch==0){
				return $exce;
			}

			if(!$exce || $stmt->rowCount()<1){
				return false;
			}

			if($fetch==2)
				return $stmt->fetchAll($fetchstyle);
			elseif ($fetch==1){
				return $stmt->fetch($fetchstyle);
			}

			return false;
		}catch (PDOException $e){
    		echoExep($e);
    		return false;
		}
    }

	/**
	 * Zählt die Einträge
	 * @param string $table
	 * Tabelle in der gezählt werden soll
	 * @param string $count
	 * Welcher Column gezählt werden soll
	 * @param string $where
	 * Bedingung was gezählt wird
	 * @param array $args
	 * Parameter für $where
	 * @return bool|mixed
	 * gibt die Anzahl oder false zurück
	 */
	public function count($table="",$count="*",$where="",$args=array()){
    	try{
			$sql="SELECT count($count) FROM $table WHERE $where";
			$stmt= $this->pdoHandle->prepare($sql);

			if(!$stmt->execute($args))
				return false;

			return $stmt->fetchColumn();
		}catch (PDOException $e){
			echoExep($e);
			return false;
		}
	}

	/**
	 * Prüft ob es einen Datensatz gibt
	 * @param string $table
	 * Prüftabelle
	 * @param string $where
	 * Bedingung, welcher Datensatz
	 * @param array $args
	 * Parameter für $where
	 * @return bool
	 * gibt true zurück wenn es den Datensatz gibt sonst false
	 */
	public function exist($table="",$where="",$args=array()){
		try{
			$sql="SELECT * FROM $table WHERE $where LIMIT 1";
			$stmt= $this->pdoHandle->prepare($sql);

			if(!$stmt->execute($args))
				return false;

			return ($stmt->rowCount()>0);
		}catch (PDOException $e){
			echoExep($e);
			return false;
		}
	}

    /**
     * Gibt PDO Objekt zurück
     * Prüft ob bereits eine offene Datenbankverbindung besteht und gibt diese zurück
     * Wenn nicht wird eine neue erstellt
     * Datenbankquery wird so aufgerufen: DB::db()->q();
     */
    public static function db() {
        if(!isset(self::$_instance)) {
            self::$_instance = self::getPDOHandler();
        }
        return self::$_instance;
    }

    //Diese Funktionen dürfen nicht aufgerufen werden von anderen Klassen
    private function __clone(){}
    private function __wakeup(){}
}