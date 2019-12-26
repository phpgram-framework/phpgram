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

use Gram\App\QueueHandler as GramQueue;
use Gram\Exceptions\MiddlewareNotAllowedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

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

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$mw = $request->getAttribute('mw',[]);

		if(count($mw)===0){
			return $this->last->handle($request);
		}

		$middleware = \array_shift($mw);

		$request = $request->withAttribute('mw',$mw);

		//wenn ein Index für die Mw angegenen wurde, siehe im Container nach
		if($this->container!==null && \is_string($middleware)){
			if($this->container->has($middleware) === false){
				throw new MiddlewareNotAllowedException("Middleware: [$middleware] not found");
			}

			$middleware = $this->container->get($middleware);
		}

		if($middleware instanceof MiddlewareInterface === false){
			throw new MiddlewareNotAllowedException("Middleware needs to implement Psr 15 MiddlewareInterface");
		}

		return $middleware->process($request,$this);	//führe die middleware aus
	}

}