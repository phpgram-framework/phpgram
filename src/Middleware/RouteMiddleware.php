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

use Gram\App\QueueHandler;
use Gram\Route\Interfaces\MiddlewareCollectorInterface;
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
	private $router,$notFoundHandler,$queueHandler,$middlewareCollector,$strategyCollector;
	private $routeid,$groupid;

	public function __construct(
		Router $router,
		RequestHandlerInterface $notFoundHandler,
		QueueHandler $queueHandler,
		MiddlewareCollectorInterface $middlewareCollector,
		StrategyCollectorInterface $strategyCollector
	){
		$this->router=$router;
		$this->notFoundHandler=$notFoundHandler;	//der handler der im errorfall getriggert werden soll
		$this->middlewareCollector=$middlewareCollector;
		$this->strategyCollector=$strategyCollector;
		$this->queueHandler=$queueHandler;
	}

	/// macht den request (normales routing)

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$uri=$request->getUri()->getPath();
		$method=$request->getMethod();

		$this->router->run($uri,$method);

		$status=$this->router->getStatus();
		$handle=$this->router->getHandle();

		//handle kann z. b. der controller als auch der 404 handle sein
		$request=$request
			->withAttribute('callable',$handle['callable'])
			->withAttribute('status',$status)
			->withAttribute('param',$this->router->getParam());

		//Bei Fehler, 404 oder 405
		if($status!==200){
			$response = $this->notFoundHandler->handle($request);	//erstelle response mit dem notfound handler
			return $response->withStatus($status);
		}

		$this->groupid=$handle['groupid'];
		$this->routeid=$handle['routeid'];

		$this->buildStack();

		//Prüfe ob es eine Route Strategie gibt
		$strategy= $this->strategyCollector->getRoute($this->routeid);
		//Wenn nicht dann ob es eine für die Gruppe gibt, die letzte Gruppenstrategie wird genommen
		if($strategy===null){
			foreach ($this->groupid as $item) {
				$check = $this->strategyCollector->getGroup($item);
				if($check!==null){
					$strategy=$check;
				}
			}
		}

		$request=$request->withAttribute('strategy',$strategy);

		return $handler->handle($request);	//wenn alles ok handle nochmal aufrufen für die nächste middleware
	}

	public function buildStack($addstd=false)
	{
		//Füge Standard Middleware hinzu (die MW die immer ausgeführt wird)
		if($addstd===true){
			foreach ($this->middlewareCollector->getStdMiddleware() as $item) {
				$this->queueHandler->add($item);
			}
		}else{
			foreach ($this->groupid as $item) {
				$grouMw=$this->middlewareCollector->getGroup($item);
				//Füge Routegroup Mw hinzu
				if ($grouMw!==null){
					foreach ($grouMw as $item2) {
						$this->queueHandler->add($item2);
					}
				}
			}

			$routeMw = $this->middlewareCollector->getRoute($this->routeid);
			//Füge Route MW hinzu
			if($routeMw!==null){
				foreach ($routeMw as $item) {
					$this->queueHandler->add($item);
				}
			}
		}
	}
}