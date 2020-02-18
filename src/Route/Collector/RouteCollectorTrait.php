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

use Gram\Route\Route;
use Gram\Route\RouteGroup;

/**
 * Trait CollectorTrait
 * @package Gram\Route\Collector
 *
 * Implementiert die unterschiedlichen Collector Methods
 */
trait RouteCollectorTrait
{
	/**
	 * Füge eine Route mit beliebiger Method hinzu
	 *
	 * @param string $path
	 * @param $handler
	 * @param array $method
	 * @return Route
	 */
	abstract function add(string $path,$handler,array $method):Route;

	/**
	 * Füge eine Gruppe hinzu
	 *
	 * @param $prefix
	 * @param callable $groupcollector
	 * @return RouteGroup
	 */
	abstract public function group($prefix,callable $groupcollector):RouteGroup;

	/**
	 * GET Route
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function get(string $route,$handler):Route
	{
		return $this->add($route,$handler,['GET']);
	}

	/**
	 * POST Route
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function post(string $route,$handler):Route
	{
		return $this->add($route,$handler,['POST']);
	}

	/**
	 * GET or POST Route
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function getpost(string $route,$handler):Route
	{
		return $this->add($route,$handler,['GET','POST']);
	}

	/**
	 * DELETE Route
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function delete(string $route,$handler):Route
	{
		return $this->add($route,$handler,['DELETE']);
	}

	/**
	 * PUT Route
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function put(string $route,$handler):Route
	{
		return $this->add($route,$handler,['PUT']);
	}

	/**
	 * PATCH Route
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function patch(string $route,$handler):Route
	{
		return $this->add($route,$handler,['PATCH']);
	}

	/**
	 * HEAD Route
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function head(string $route,$handler):Route
	{
		return $this->add($route,$handler,['HEAD']);
	}

	/**
	 * OPTIONS Route
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function options(string $route,$handler):Route
	{
		return $this->add($route,$handler,['OPTIONS']);
	}

	/**
	 * Route with every method
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function any(string $route,$handler):Route
	{
		return $this->add($route,$handler,['GET','POST','DELETE','PUT','PATCH','HEAD','OPTIONS']);
	}
}