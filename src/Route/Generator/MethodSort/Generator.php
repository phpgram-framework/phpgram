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

use Gram\Route\Interfaces\GeneratorInterface;
use Gram\Route\Route;

abstract class Generator implements GeneratorInterface
{
	const CHUNKSIZE = 10;

	private $dynamic=[];
	private $static=[];

	public function generate(array $routes)
	{
		foreach ($routes as $i=>$route) {
			$this->mapRoute($route);
		}

		$dynamic = $this->generateDynamic($this->dynamic);	//Genereire Dynamic Routemap

		return ['static'=>$this->static,'dynamic'=>$dynamic];
	}

	/**
	 * Unterteile die Routes in static und dynamic
	 *
	 * Führe dazu den Routeparser im Route Objekt aus
	 *
	 * @param Route $route
	 */
	private function mapRoute(Route $route)
	{
		$route->createRoute();	//parse die Route

		//Ordne die Route in Static und Dynamic
		if (\count($route->vars)===0){
			$typ = 0;
		}else{
			$typ = 1;
		}

		foreach ($route->method as $item) {
			if($typ===0){
				$this->static[$item][$route->path]=$route->handle;
			}elseif ($typ===1){
				$this->dynamic[$item][]=$route;
			}
		}
	}
}