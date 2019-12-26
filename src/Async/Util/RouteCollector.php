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

namespace Gram\Async\Util;

use Gram\Route\Collector\RouteCollector as GramRouteCollector;

/**
 * Class RouteCollector
 * @package Gram\Async\Util
 *
 * Ein Route Collector für Async Requests
 *
 * generiert die Routes nur ein mal
 */
class RouteCollector extends GramRouteCollector
{
	protected $data;

	public function getData():array
	{
		if(!isset($this->data)){
			$this->data = $this->generator->generate($this->routes);
		}

		return $this->data;
	}
}