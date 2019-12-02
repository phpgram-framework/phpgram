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

use Gram\Route\Interfaces\GeneratorInterface;
use Gram\Route\Route;

/**
 * Class Generator
 * @package Gram\Route\Generator
 *
 * Hauptgenerator wird für die static Routes genutzt
 *
 * Trennt zuerst die static von den dynamischen Routes
 *
 * Fügt die static Routes dem Array hinzu
 *
 * Führt danach den Dynamischen Generator aus
 */
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

		$this->dynamic=$this->generateDynamic($this->dynamic);	//Genereire Dynamic Routemap

		return ['static'=>$this->static,'dynamic'=>$this->dynamic];
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

		$route->handle['method']=$route->method;	//setze die Method in den Handle ein

		//Ordne die Route in Static und Dynamic
		if (\count($route->vars)===0){
			$this->static[$route->path]=$route->handle;
		}else{
			$this->dynamic[]=$route;
		}
	}
}