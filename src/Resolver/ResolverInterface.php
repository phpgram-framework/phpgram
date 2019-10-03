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

namespace Gram\Resolver;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface Callback
 * @package Gram\Resolver
 *
 * Interface für die Callback Klassen die ein Callable erstellen und ausführen
 */
interface ResolverInterface
{
	/**
	 * Führt das Callback aus mit den Parameter aus dem Request und dem Request Object
	 *
	 * Erwartet den Rückgabe des Callbacks
	 *
	 * @param array $param
	 * @return mixed
	 */
	public function resolve($param=[]);

	/**
	 * Erstellt das Callback
	 *
	 * @return mixed
	 */
	public function set();

	/**
	 * Gibt das Request Object
	 *
	 * @param ServerRequestInterface $request
	 * @return mixed
	 */
	public function setRequest(ServerRequestInterface $request);

	/**
	 * Gibt das Response Object
	 *
	 * @param ResponseInterface $response
	 * @return mixed
	 */
	public function setResponse(ResponseInterface $response);

	/**
	 * Nimmt das Response Object wieder zurück
	 *
	 * @return ResponseInterface
	 */
	public function getResponse():ResponseInterface;

	/**
	 * Wenn ein Container genutzt wird, gebe den Container dem Resolver
	 *
	 * @param ContainerInterface|null $container
	 * @return mixed
	 */
	public function setContainer(ContainerInterface $container=null);
}