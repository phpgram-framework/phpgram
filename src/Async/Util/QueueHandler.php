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

namespace Gram\Async\Util;

use Gram\Middleware\QueueHandler as GramQueue;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class QueueHandler
 * @package Gram\Async\Util
 *
 * Ein QueueHandler für Async Requests
 *
 * Holt sich die Mw aus dem Request Object
 */
class QueueHandler extends GramQueue
{

	/**
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 * @throws \Gram\Exceptions\MiddlewareNotAllowedException
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		/** @var array $mw */
		$mw = $request->getAttribute('mw',[]);

		if(\count($mw) === 0) {
			return $this->last->handle($request);
		}

		$middleware = \array_shift($mw);

		$request = $request->withAttribute('mw',$mw);

		$middleware = $this->checkMiddleware($middleware);

		return $middleware->process($request,$this);	//führe die middleware aus
	}

}