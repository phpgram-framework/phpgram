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

namespace Gram\Async\App;

use Gram\App\App;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AsyncApp
 * @package Gram\Async\App
 *
 * Die App die genommen wird für Async Requests
 *
 * Baut Mw in den Request ein
 *
 * Benutzt andere Klassen
 */
class AsyncApp extends App
{
	private static $_instance;

	private function __construct()
	{
	}

	public function build()
	{
		$this->setRawOptions([
			'queue_handler'=>'Gram\\Async\\Util\\QueueHandler',
			'routeMw'=>'Gram\\Async\\Util\\RoutingMiddleware'
		]);

		parent::build();
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$mw = $this->middlewareCollector->getStdMiddleware();
		$mw[] = $this->routeMiddleware;

		$request = $request->withAttribute('mw',$mw);

		return parent::handle($request);
	}

	public function buildAsyncStack(ServerRequestInterface $request, int $routeid = null, array $groupid = null):ServerRequestInterface
	{
		if($routeid===null || $groupid===null){
			return $request;
		}

		$mw = [];

		foreach ($groupid as $item) {
			$grouMw=$this->middlewareCollector->getGroup($item);
			//Füge Routegroup Mw hinzu
			if ($grouMw!==null){
				foreach ($grouMw as $item2) {
					$mw[] = $item2;
				}
			}
		}

		$routeMw = $this->middlewareCollector->getRoute($routeid);
		//Füge Route MW hinzu
		if($routeMw!==null){
			foreach ($routeMw as $item) {
				$mw[] = $item;
			}
		}

		return $request->withAttribute('mw',$mw);
	}

	public static function app()
	{
		if(!isset(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function getRouter()
	{
		if(!isset($this->router)){
			$this->router_options +=[
				'collector'=>'Gram\\Async\\Util\\RouteCollector'
			];
		}

		return parent::getRouter();
	}
}