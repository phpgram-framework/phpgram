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
		$uri=$request->getUri()->getPath();
		$method=$request->getMethod();

		[$status,$handle,$param] = $this->router->run($uri,$method);

		//handle kann z. b. der controller als auch der 404 handle sein
		$request=$request
			->withAttribute('callable',$handle['callable'])
			->withAttribute('status',$status)
			->withAttribute('param',$param);

		//Bei Fehler, 404 oder 405
		if($status!==200){
			return $this->notFoundHandler->handle($request);	//erstelle response mit dem notfound handler
		}

		$routeid = $handle['routeid'];
		$groupid = $handle['groupid'];

		$request = $this->app->buildAsyncStack($request,$routeid,$groupid);

		//Prüfe ob es eine Route Strategie gibt
		$strategy= $this->strategyCollector->getRoute($routeid);
		//Wenn nicht dann ob es eine für die Gruppe gibt, die letzte Gruppenstrategie wird genommen
		if($strategy===null){
			foreach ($groupid as $item) {
				$check = $this->strategyCollector->getGroup($item);
				if($check!==null){
					$strategy=$check;
				}
			}
		}

		$request=$request->withAttribute('strategy',$strategy);

		return $handler->handle($request);	//wenn alles ok handle nochmal aufrufen für die nächste middleware
	}
}