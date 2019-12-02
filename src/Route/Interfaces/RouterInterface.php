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
 * Interface RouterInterface
 * @package Gram\Route\Interfaces
 *
 * Ein Interface für den Router
 */
interface RouterInterface
{
	/**
	 * Starte die Dispatcher
	 *
	 * Wenn gesetzt prüfe die http Method
	 *
	 * Wenn Route gefunden gebe Handler zurück
	 *
	 * Sollte ein Fehler auftauchen (404 oder 405) Gebe diese Handler zurück
	 *
	 * @param $uri
	 * @param $httpMethod
	 * @return mixed
	 */
	public function run($uri,$httpMethod);

	/**
	 * @return CollectorInterface
	 */
	public function getCollector();
}