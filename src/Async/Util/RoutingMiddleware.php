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

use Gram\Async\App\AsyncApp;
use Gram\Middleware\RouteMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RoutingMiddleware
 * @package Gram\Async\Util
 *
 * Eine Routeing Mw für Async Requests
 *
 * Genau wie die normale, nur, dass die Mws der Route in den Request gepackt werden
 */
class RoutingMiddleware extends RouteMiddleware
{
	/** @var AsyncApp */
	protected $app;

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		[$request,$status,$handle] = $this->route($request);

		//Bei Fehler, 404 oder 405
		if($status!==200){
			return $this->notFoundHandler->handle($request);	//erstelle response mit dem notfound handler
		}

		$routeid = $handle['routeid'];
		$groupid = $handle['groupid'];

		$request = $this->app->buildAsyncStack($request,$routeid,$groupid);

		$strategy = $this->getStrategy($routeid,$groupid);

		$request=$request->withAttribute('strategy',$strategy);

		return $handler->handle($request);	//wenn alles ok handle nochmal aufrufen für die nächste middleware
	}
}