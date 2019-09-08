<?php
/**
 * phpgram
 *
 * This File is part of the phpgram Micro Framework
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE
 *
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\Route\Interfaces;

/**
 * Interface Dispatcher
 * @package Gram\Route\Dispatcher
 *
 * Ein Interface das alle Dispatcher implementieren müssen
 */
interface DispatcherInterface
{
	const FOUND=1;
	const NOT_FOUND = 0;

	public function setData(array $routes);

	/**
	 * Pürfe ob die Route in dem Array ist (static Route)
	 *
	 * Sonst führe den Dynamischen Dispatcher aus
	 *
	 * @param $uri
	 * @return array
	 */
	public function dispatch($uri);

	/**
	 * Suche jede Gruppenregex ab
	 *
	 * Wenn die richtige Routeregex gefunden wird der Handler
	 *
	 * (
	 * dieser steht in seinem Array an der
	 *
	 * gleichen Stelle wie die Route in der Regexliste
	 *
	 * $handle= $handlerListe[Regex_Liste_Nummer][Platz_in_der_Regex]
	 *
	 * Platz in der Regex wird durch die Anzahl an matches bestimmt (die stimmt dank der Placeholder, die der Generator erstellt, überein
	 * )
	 *
	 * und die Matches zurück gegeben
	 *
	 * Sonst gebe Not_Found Fehler zurück
	 *
	 * @param string $uri
	 * Die Uri die geprüft werden soll (hier als Url behandelt)
	 * @param array $routes
	 * @param array $handler
	 * @return array
	 */
	public function dispatchDynamic($uri, array $routes,array $handler);
}