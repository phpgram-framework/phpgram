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
use Gram\Route\Route;
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

	/**
	 * RouteMiddleware constructor.
	 * @param Router $router
	 * @param RequestHandlerInterface $notFoundHandler
	 * @param App $app
	 */
	public function __construct(
		Router $router,
		RequestHandlerInterface $notFoundHandler,
		App $app
	){
		$this->router = $router;
		$this->notFoundHandler = $notFoundHandler;	//der handler der im errorfall getriggert werden soll
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

		/**
		 * @var int $status
		 * @var Route|mixed $route
		 * @var array $param
		 */
		[$status,$route,$param] = $this->router->run($uri,$method);

		//$route kann z. b. Route sein als auch der 404 handle sein
		$request = $request
			->withAttribute(self::CALLABLE, $status !== 200 ? $route : $route->handle)
			->withAttribute(self::STATUS,$status)
			->withAttribute(self::ROUTE_PARAMETER,$param);

		//Bei Fehler, 404 oder 405
		if($status !== 200){
			return $this->notFoundHandler->handle($request);	//erstelle response mit dem notfound handler
		}

		$this->app->buildStack($request,$route);

		$request = $request->withAttribute(self::ROUTE_STRATEGY,$route->getStrategy());

		return $handler->handle($request);	//wenn alles ok handle nochmal aufrufen für die nächste middleware
	}
}