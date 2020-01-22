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

namespace Gram\Route\Generator;

use Gram\Route\Route;

/**
 * Class MarkBased
 * @package Gram\Route\Generator
 *
 * Based on:
 * http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
 * https://github.com/nikic/FastRoute
 */
class MarkBased extends Generator
{
	use DynamicGeneratorTrait;

	/**
	 * @inheritdoc
	 */
	protected function getChunkSize():int
	{
		return 30;
	}

	/**
	 * @inheritdoc
	 *
	 * Sortiert die Routes nach Marks
	 */
	protected function chunkRoutes(array &$chunk,$method)
	{
		$markName = 'a';
		$routeCollector = [];
		$handleCollector = [];

		/** @var Route $route */
		foreach ($chunk as $route) {
			$routeCollector[] = $route->path. '(*MARK:' . $markName . ')';
			$handleCollector[$markName] = [$route->routeid,$route->vars];
			++$markName;
		}

		$this->routeList[$method][] = '~^(?|' . \implode('|', $routeCollector) . ')$~x'; //wandle die Routes in ein gemeinsames Regex um
		$this->handlerList[$method][] = $handleCollector;	//übergibt die handler für die Routeliste
	}
}