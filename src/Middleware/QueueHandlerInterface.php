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

namespace Gram\Middleware;

use Gram\Middleware\Queue\QueueInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Interface QueueHandlerInterface
 * @package Gram\Middleware
 *
 * QueueHandler ist verantwortlich für die Middleware Reihenfolge und
 * Ausführung
 */
interface QueueHandlerInterface extends RequestHandlerInterface
{
	/**
	 * Füge für den akutellen Request eine Middleware hinzu
	 *
	 * @param ServerRequestInterface $request
	 * @param $middleware
	 * @return void
	 */
	public function add(ServerRequestInterface $request, $middleware);

	/**
	 * Gebe den Handler zurück der ausgeführt wird,
	 * wenn alle Mw durchgelaufen sind
	 *
	 * @return RequestHandlerInterface
	 */
	public function getLast():RequestHandlerInterface;

	/**
	 * Gebe das @see QueueInterface Object für den akutellen Request zurück
	 *
	 * @param ServerRequestInterface $request
	 * @return QueueInterface
	 */
	public function getQueue(ServerRequestInterface $request):QueueInterface;

	/**
	 * Funktioniert wie @see handle() 
	 * 
	 * QueueHandler lässt sich somit als callable aufrufen
	 * 
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 */
	public function __invoke(ServerRequestInterface $request): ResponseInterface;
}