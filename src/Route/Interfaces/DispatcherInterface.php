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
 * @author Jörn Heinemann <joernheinemann@gmx.de>
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
	const FOUND = 200;
	const NOT_FOUND = 404;
	const NOT_ALLOWED = 405;

	/**
	 * Pürfe ob die Route in dem Array ist (static Route)
	 *
	 * Sonst führe den Dynamischen Dispatcher aus
	 *
	 * @param $method
	 * @param $uri
	 * @return array
	 */
	public function dispatch($method,$uri);

	/**
	 * Suche jede Gruppenregex ab
	 *
	 * Wenn die richtige Routeregex gefunden wird der Handler
	 * und die Matches zurück gegeben
	 *
	 * Sonst gebe Not_Found Fehler zurück
	 *
	 * @param $method
	 * @param string $uri
	 * Die Uri die geprüft werden soll (hier als Url behandelt)
	 * @return array
	 */
	public function dispatchDynamic($method, $uri);
}