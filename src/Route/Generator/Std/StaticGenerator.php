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

namespace Gram\Route\Generator\Std;

use Gram\Route\Generator\Generator;
use Gram\Route\Route;

/**
 * Class StaticGenerator
 * @package Gram\Route\Generator\Std
 *
 * Der Static Generator für alle unterschiedlichen Dynamic Generator
 */
abstract class StaticGenerator extends Generator
{
	/**
	 * Unterteile die Routes in static und dynamic
	 *
	 * Führe dazu den Routeparser im Route Objekt aus
	 *
	 * @param Route $route
	 */
	public function mapRoute(Route $route)
	{
		[$route->path,$route->vars] = $this->createRoute($route->path);	//parse die Route

		$route->handle['method']=$route->method;	//setze die Method in den Handle ein

		//Ordne die Route in Static und Dynamic
		if (\count($route->vars)===0){
			$this->static[$route->path]=$route->handle;
		}else{
			$this->dynamic[]=$route;
		}
	}
}