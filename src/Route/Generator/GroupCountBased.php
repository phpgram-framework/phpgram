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
 * Class GroupCountBased
 * @package Gram\Route\Generator\MethodSort
 *
 * Ein Generator der die Routes ihrer Method zuordnet
 *
 * Based on:
 * http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
 * https://github.com/nikic/FastRoute
 */
class GroupCountBased extends Generator
{
	use DynamicGeneratorTrait;

	protected function getChunkSize():int
	{
		return 10;
	}

	protected function chunkRoutes(array &$chunk,$method)
	{
		$number = 0;
		$routeCollector = [];
		$handleCollector = [];

		/** @var Route $route */
		foreach ($chunk as $route) {
			$varcount = \count($route->vars);	//zähle die Varaiblen die die Funktion erwartet (für Placeholder: () )
			$number = \max($number,$varcount);	//passe Placeholderanzahl an

			$routeCollector[] = $route->path. \str_repeat('()', $number - $varcount);	//gruppiere die routes, füge placeholder hinzu abzgl. der Varialben
			++$number;	//erhöhe da die nächste Route einen Playerholder mehr braucht
			$handleCollector[$number] = [$route->routeid,$route->vars];	//gruppiere die Handler an der gleichen Stelle wie die Regex, hier number +1 da der Match mindestens bei 1 anfängt
		}

		$this->routeList[$method][] = '~^(?|' . \implode('|', $routeCollector) . ')$~x'; //wandle die Routes in ein gemeinsames Regex um
		$this->handlerList[$method][] = $handleCollector;	//übergibt die handler für die Routeliste
	}

}