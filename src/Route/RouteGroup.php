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
 * Class RouteGroup
 * @package Gram\Route
 *
 * Ein Route Group Objekt um Middleware und Strategy für die Gruppe hinzu zufügen
 */
class RouteGroup
{
	/** @var int */
	private $groupid;

	/** @var array */
	private static $middleware = [];

	/** @var array  */
	private static $strategy = [];

	/**
	 * RouteGroup constructor.
	 * @param $groupid
	 */
	public function __construct($groupid)
	{
		$this->groupid = $groupid;
	}

	public function addMiddleware($middleware)
	{
		self::$middleware[$this->groupid][] = $middleware;

		return $this;
	}

	public function addStrategy($strategy)
	{
		self::$strategy[$this->groupid] = $strategy;

		return $this;
	}

	public static function getMiddleware(int $groupId): array
	{
		return self::$middleware[$groupId] ?? [];
	}

	public static function getStrategy(int $groupId)
	{
		return self::$strategy[$groupId] ?? null;
	}
}