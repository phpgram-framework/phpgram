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
	public function resolve(array $param);

	/**
	 * Erstellt das Callback
	 *
	 * @return void
	 */
	public function set(): void;

	/**
	 * Gibt das Request Object
	 *
	 * @param ServerRequestInterface $request
	 * @return void
	 */
	public function setRequest(ServerRequestInterface $request): void;

	/**
	 * Gibt das Response Object
	 *
	 * @param ResponseInterface $response
	 * @return void
	 */
	public function setResponse(ResponseInterface $response): void;

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
	 * @return void
	 */
	public function setContainer(ContainerInterface $container=null): void;
}