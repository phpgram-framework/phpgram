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

namespace Gram\Callback;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface Callback
 * @package Gram\Callback
 *
 * Interface für die Callback Klassen die ein Callable erstellen und ausführen
 */
interface CallbackInterface
{
	/**
	 * Führt das Callback aus mit den Parameter aus dem Request und dem Request Object
	 *
	 * Erwartet den Rückgabe des Callbacks
	 *
	 * @param array $param
	 * @param ServerRequestInterface $request
	 * @return mixed
	 */
	public function callback($param=[],ServerRequestInterface $request);

	/**
	 * Gibt den Request der bei @see callback() übergeben wurde zurück
	 *
	 * @return ServerRequestInterface
	 */
	public function getRequest():ServerRequestInterface;

	/**
	 * Erstellt das Callback
	 *
	 * @return mixed
	 */
	public function set();
}