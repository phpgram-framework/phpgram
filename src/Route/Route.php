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

namespace Gram\Route;

/**
 * Class Route
 * @package Gram\Route
 *
 * Ein Routeobjekt, dass Informationen über die Route enthält
 *
 * wird vom Routecollecotr aufgerufen wenn eine neue Route hinzugefügt wird
 *
 * Bietet die Möglichkeit Middleware und Strategy für die Route hinzu zufügen
 */
class Route
{
	public $path, $handle, $vars = [], $groupid = [], $routeid, $method;

	/** @var array */
	private $middleware = [];

	/** @var \Gram\Strategy\StrategyInterface|string  */
	private $strategy;

	/**
	 * @var bool
	 * gebe an, dass die Route all ihre Middleware geholt hat
	 */
	private $routeReady = false;

	/**
	 * Route constructor.
	 * @param string $path
	 * @param mixed $handle
	 * @param string [] $method
	 * @param int[] $routegroupid
	 * @param int $routeid
	 */
	public function __construct(
		string $path,
		$handle,
		$method,
		$routegroupid,
		$routeid
	){
		$this->method = $method;	//speichere Method für Dispatcher
		$this->path = $path;
		$this->handle = $handle;
		$this->groupid = $routegroupid;
		$this->routeid = $routeid;
	}

	/**
	 * Erstellt eine neue Route mit neuem Path aber den alten Werten
	 *
	 * ohne Collectoren und Handler!
	 *
	 * @param string $newPath		der neue Path
	 * @return Route
	 */
	public function cloneRoute(string $newPath)
	{
		return new Route(
			$newPath,
			$this->routeid,
			$this->method,
			$this->groupid,
			$this->routeid
		);
	}

	/**
	 * Füge eine Middleware der Route hinzu
	 *
	 * @param $middleware
	 * @return $this
	 */
	public function addMiddleware($middleware)
	{
		$this->middleware[] = $middleware;

		return $this;
	}

	/**
	 * Gebe alle Middleware wieder zurück
	 *
	 * @return array
	 */
	public function getRouteMiddleware(): array
	{
		if(!$this->routeReady) {
			$this->prepareRoute();
		}

		return $this->middleware;
	}

	/**
	 * Füge eine Strategy hinzu
	 *
	 * @param \Gram\Strategy\StrategyInterface|string $strategy
	 * @return $this
	 */
	public function addStrategy($strategy)
	{
		$this->strategy = $strategy;

		return $this;
	}

	/**
	 * @return mixed|null
	 */
	public function getStrategy()
	{
		if(!$this->routeReady) {
			$this->prepareRoute();
		}

		return $this->strategy;
	}

	/**
	 * Erstelle den Route Middleware Stack incl. der Group Middleware
	 *
	 * Behalte diesen Stack bei
	 */
	protected function prepareRoute()
	{
		$groupMws = [];

		foreach ($this->groupid as $item) {
			$groupMw = RouteGroup::getMiddleware($item);
			//Füge Routegroup Mw hinzu
			if ($groupMw !== null){
				foreach ($groupMw as $mw) {
					$groupMws[] = $mw;
				}
			}

			if(!isset($this->strategy)) {
				//suche nach einer strategy in den groups nur dann wenn die route keine hat
				//nehme immer die zuletzt hinzugefügte strategy
				$check = RouteGroup::getStrategy($item);

				if($check !== null){
					$strategy = $check;
				}
			}
		}

		if(!isset($this->strategy)) {
			$this->strategy = $strategy ?? null;
		}

		$this->middleware = \array_merge($groupMws,$this->middleware);

		$this->routeReady = true;
	}
}