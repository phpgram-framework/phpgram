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
 * @author Jörn Heinemann <j.heinemann1@web.de>
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
	private $std=[],$route=[],$group=[];

	public function addStd($middleware, $order = null)
	{
		$this->std[]=$middleware;
		return $this;
	}

	public function addRoute($routeid, $middleware, $order = null)
	{
		$this->route[$routeid][]=$middleware;
	}

	public function addGroup($groupid, $middleware, $order = null)
	{
		$this->group[$groupid][]=$middleware;
	}

	public function getStdMiddleware()
	{
		return $this->std;
	}

	public function getGroup($id)
	{
		if(isset($this->group[$id]))
			return $this->group[$id];
	}

	public function getRoute($id)
	{
		if(isset($this->route[$id]))
			return $this->route[$id];
	}
}