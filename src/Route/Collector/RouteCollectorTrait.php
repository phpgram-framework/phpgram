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

/**
 * Trait CollectorTrait
 * @package Gram\Route\Collector
 *
 * Implementiert die unterschiedlichen Collector Methods
 */
trait RouteCollectorTrait
{
	abstract function add(string $path,$handler,array $method);

	abstract public function addGroup($prefix,callable $groupcollector);

	public function get(string $route,$handler)
	{
		return $this->add($route,$handler,['GET']);
	}

	public function post(string $route,$handler)
	{
		return $this->add($route,$handler,['POST']);
	}

	public function getpost(string $route,$handler)
	{
		return $this->add($route,$handler,['GET','POST']);
	}

	public function head(string $route,$handler)
	{
		return $this->add($route,$handler,['HEAD']);
	}

	public function delete(string $route,$handler)
	{
		return $this->add($route,$handler,['DELETE']);
	}

	public function put(string $route,$handler)
	{
		return $this->add($route,$handler,['PUT']);
	}

	public function patch(string $route,$handler)
	{
		return $this->add($route,$handler,['PATCH']);
	}

	public function options(string $route,$handler)
	{
		return $this->add($route,$handler,['OPTIONS']);
	}

	public function any(string $route,$handler)
	{
		return $this->add($route,$handler,['GET','POST','DELETE','PUT','PATCH','OPTIONS']);
	}
}