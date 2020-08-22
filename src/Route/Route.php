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
	private static $middleware = [];

	/** @var array  */
	private static $strategy = [];

	/**
	 * Route constructor.
	 * @param string $path
	 * @param $handle
	 * @param $method
	 * @param $routegroupid
	 * @param $routeid
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

	public function addMiddleware($middleware)
	{
		self::$middleware[$this->routeid][] = $middleware;

		return $this;
	}

	public function addStrategy($strategy)
	{
		self::$strategy[$this->routeid] = $strategy;

		return $this;
	}

	/**
	 * @param int $routeId
	 * @return array
	 */
	public static function getMiddleware(int $routeId): array
	{
		return self::$middleware[$routeId] ?? [];
	}

	/**
	 * @param int $routeId
	 * @return mixed|null
	 */
	public static function getStrategy(int $routeId)
	{
		return self::$strategy[$routeId] ?? null;
	}
}