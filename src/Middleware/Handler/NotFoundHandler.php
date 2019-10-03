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

namespace Gram\Middleware\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class NotFoundHandler
 * @package Gram\Middleware\Handler
 *
 * Ein Handler der von der Routing Middleware aufgerufen wird, sollte 404 oder 405 eintreten
 */
class NotFoundHandler implements RequestHandlerInterface
{
	private $callbackHandler;

	public function __construct(RequestHandlerInterface $callbackHandler)
	{
		$this->callbackHandler=$callbackHandler;
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return $this->callbackHandler->handle($request);
	}
}