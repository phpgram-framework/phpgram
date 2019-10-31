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

use Gram\App\App;
use Gram\Route\Interfaces\StrategyCollectorInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Gram\Route\Interfaces\RouterInterface as Router;

/**
 * Class RouteMiddleware
 * @package Gram\Middleware
 *
 * Die Middleware die nach den Std Middleware ausgeführt wird
 *
 * Führt das Routing aus mithilfe des Routers
 *
 * Fügt nach dem Routing weitere Informationen dem Request hinzu
 *
 * Fügt weitere Middlewares von den Routes / Routegroups hinzu
 *
 * Zuerst werden die Group Middleware hinzugefügt dann die Route Middleware
 */
class RouteMiddleware implements MiddlewareInterface
{
	protected $router,$notFoundHandler,$app,$strategyCollector;

	public function __construct(
		Router $router,
		RequestHandlerInterface $notFoundHandler,
		App $app,
		StrategyCollectorInterface $strategyCollector
	){
		$this->router=$router;
		$this->notFoundHandler=$notFoundHandler;	//der handler der im errorfall getriggert werden soll
		$this->strategyCollector=$strategyCollector;
		$this->app=$app;
	}

	/// macht den request (normales routing)

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

		$this->app->buildStack($routeid,$groupid);

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