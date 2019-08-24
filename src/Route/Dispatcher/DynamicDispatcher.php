<?php
namespace Gram\Route\Dispatcher;
use Gram\Route\Map\Map;

/**
 * Class DynamicDispatcher
 * @package Gram\Route\Dispatcher
 * @author Jörn Heinemann
 * Sucht den richtigen Handler, anhand der aufgerufenen Url, in der übergebenen Regexliste
 * Arbeitet nach dem Group Count Based (GCB) Prinzip
 * Wird von den Router Klassen aufgerufen um ein Request durch zuführen
 * Anlehnung an: http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
 */
class DynamicDispatcher implements Dispatcher
{
	private $regex,$handler;

	public function __construct(Map $map){
		$map=$map->getMap();
		$this->regex=$map['regexes'];
		$this->handler=$map['dynamichandler'];
	}

	/**
	 * Suche jede Gruppenregex ab
	 * Wenn die richtige Routeregex gefunden wird der Handler
	 * (
	 * dieser steht in seinem Array an der
	 * gleichen Stelle wie die Route in der Regexliste
	 * $handle= $handlerListe[Regex_Liste_Nummer][Platz_in_der_Regex]
	 * Platz in der Regex wird durch die Anzahl an matches bestimmt (die stimmt dank der Placeholder, die der Generator erstellt, überein
	 * )
	 * und die Matches zurück gegeben
	 * Sonst gebe Not_Found Fehler zurück
	 * @param string $uri
	 * Die Uri die geprüft werden soll (hier als Url behandelt)
	 * @return array
	 */
	public function dispatch($uri){
		//durchlaufe die Regexlisten
		//$i = welche Regexliste
		//count($matches) = nummer des handlers
		foreach($this->regex as $i=>$regex) {
			if(!preg_match($regex,$uri,$matches)){
				continue;	//wenn Route nicht Dabei ist nächsten Chunk prüfen
			}

			//wenn Route im Chunk war
			$handle = $this->handler[$i][count($matches)];
			unset($matches[0]);	//erstes Element (der komplette Match) entfernen

			return array(self::FOUND,$handle,$matches);
		}
		return array(self::NOT_FOUND);
	}
}