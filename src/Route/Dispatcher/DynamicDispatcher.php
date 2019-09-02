<?php
namespace Gram\Route\Dispatcher;

/**
 * Class DynamicDispatcher
 * @package Gram\Route\Dispatcher
 * @author Jörn Heinemann
 * Sucht den richtigen Handler, anhand der aufgerufenen Url, in der übergebenen Regexliste
 * Arbeitet nach dem Group Count Based (GCB) Prinzip
 * Wird von den Router Klassen aufgerufen um ein Request durch zuführen
 * Anlehnung an:
 * http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
 * https://github.com/nikic/FastRoute
 */
class DynamicDispatcher extends Dispatcher
{
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
	 * @param array $routes
	 * @param array $handler
	 * @return array
	 */
	public function dispatchDynamic($uri,array $routes,array $handler){
		//durchlaufe die Regexlisten
		//$i = welche Regexliste
		//count($matches) = nummer des handlers
		foreach($routes as $i=>$regex) {
			if(!preg_match($regex,$uri,$matches)){
				continue;	//wenn Route nicht Dabei ist nächsten Chunk prüfen
			}

			//wenn Regex im Chunk war
			$route = $handler[$i][count($matches)];

			$var=[];
			foreach ($route[1] as $j=>$item) {
				$var[$item]=$matches[$j+1];
			}

			return [self::FOUND,$route[0],$var];	//[status,handler,vars}
		}
		return [self::NOT_FOUND];
	}
}