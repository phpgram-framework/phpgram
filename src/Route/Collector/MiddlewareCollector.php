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

namespace Gram\Route\Collector;

use Gram\Route\Interfaces\MiddlewareCollectorInterface;

/**
 * Class MiddlewareCollector
 * @package Gram\Route\Collector
 *
 * Ein Sammler für Middleware
 *
 * Std sind die Middleware die unabhänig vom Routing ausgeführt werden (werden vor dem Routing ausgeführt)
 *
 * Group Middleware wird für Routegruppen ausgeführt (zuerst die erste Gruppe)
 *
 * Route Middleware wird speziell für die Route ausgeführt
 */
class MiddlewareCollector implements MiddlewareCollectorInterface
{
	/** @var array */
	private $std = [];

	/** @var array */
	private $route = [];

	/** @var array */
	private $group = [];

	/**
	 * @inheritdoc
	 */
	public function addStd($middleware)
	{
		$this->std[]=$middleware;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function addRoute($routeid, $middleware)
	{
		$this->route[$routeid][]=$middleware;
	}

	/**
	 * @inheritdoc
	 */
	public function addGroup($groupid, $middleware)
	{
		$this->group[$groupid][]=$middleware;
	}

	/**
	 * @inheritdoc
	 */
	public function getStdMiddleware()
	{
		return $this->std;
	}

	/**
	 * @inheritdoc
	 */
	public function getGroup($id)
	{
		return $this->group[$id] ?? null;
	}

	/**
	 * @inheritdoc
	 */
	public function getRoute($id)
	{
		return $this->route[$id] ?? null;
	}
}