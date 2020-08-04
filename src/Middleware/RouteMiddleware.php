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
use Gram\App\Route\StrategyCollectorInterface;
use Gram\Route\Interfaces\RouteInterface;
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
	const CALLABLE = "callable";

	const STATUS = "status";

	const ROUTE_PARAMETER = "param";

	const ROUTE_STRATEGY = "strategy";

	/** @var Router */
	protected $router;

	/** @var RequestHandlerInterface */
	protected $notFoundHandler;

	/** @var App */
	protected $app;

	/** @var StrategyCollectorInterface */
	protected $strategyCollector;

	/**
	 * RouteMiddleware constructor.
	 * @param Router $router
	 * @param RequestHandlerInterface $notFoundHandler
	 * @param App $app
	 * @param StrategyCollectorInterface $strategyCollector
	 */
	public function __construct(
		Router $router,
		RequestHandlerInterface $notFoundHandler,
		App $app,
		StrategyCollectorInterface $strategyCollector
	){
		$this->router = $router;
		$this->notFoundHandler = $notFoundHandler;	//der handler der im errorfall getriggert werden soll
		$this->strategyCollector = $strategyCollector;
		$this->app = $app;
	}

	/**
	 * @inheritdoc
	 *
	 * Führe den Router aus
	 *
	 * Setze mithilfe der @see App
	 * die Mw der Route in die Queue ein
	 *
	 * Setze die Strategy der Route in den Request ein
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$uri = $request->getUri()->getPath();
		$method = $request->getMethod();

		/** @var RouteInterface $handle */
		[$status,$handle,$param] = $this->router->run($uri,$method);

		//handle kann z. b. der controller als auch der 404 handle sein
		$request = $request
			->withAttribute(self::STATUS,$status)
			->withAttribute(self::ROUTE_PARAMETER,$param);

		//Bei Fehler, 404 oder 405
		if($status !== 200){
			$request = $request->withAttribute(self::CALLABLE,$handle);

			return $this->notFoundHandler->handle($request);	//erstelle response mit dem notfound handler
		} else {
			$request = $request->withAttribute(self::CALLABLE,$handle->getHandle());
		}

		$routeid = $handle->getRouteId();
		$groupid = $handle->getGroupId();

		$this->app->buildStack($request,$routeid,$groupid);

		$strategy = $this->getStrategy($routeid,$groupid);

		$request = $request->withAttribute(self::ROUTE_STRATEGY,$strategy);

		return $handler->handle($request);	//wenn alles ok handle nochmal aufrufen für die nächste middleware
	}

	/**
	 * Hole Strategy von Collector für die Route
	 *
	 * @param $routeid
	 * @param $groupid
	 * @return mixed
	 */
	protected function getStrategy($routeid,$groupid)
	{
		//Prüfe ob es eine Route Strategie gibt
		$strategy = $this->strategyCollector->getRoute($routeid);

		//Wenn nicht dann ob es eine für die Gruppe gibt, die letzte Gruppenstrategie wird genommen
		if($strategy === null){
			foreach ($groupid as $item) {
				$check = $this->strategyCollector->getGroup($item);
				if($check !== null){
					$strategy=$check;
				}
			}
		}

		return $strategy;
	}
}