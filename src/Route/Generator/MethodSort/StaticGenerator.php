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

namespace Gram\Route\Generator\MethodSort;

use Gram\Route\Generator\Generator;
use Gram\Route\Route;

/**
 * Class StaticGenerator
 * @package Gram\Route\Generator\MethodSort
 *
 * Der Static Generator für alle methodSort Generator
 */
abstract class StaticGenerator extends Generator
{
	public function mapRoute(Route $route)
	{
		[$route->path,$route->vars] = $this->createRoute($route->path);	//parse die Route

		//Ordne die Route in Static und Dynamic
		$typ = \count($route->vars) === 0 ? 0 : 1;

		foreach ($route->method as $item) {
			if($typ===0){
				$this->static[$item][$route->path]=$route->handle;
			}elseif ($typ===1){
				$this->dynamic[$item][]=$route;
			}
		}
	}
}