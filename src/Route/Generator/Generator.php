<?php
namespace Gram\Route\Generator;
use Gram\Route\Interfaces\GeneratorInterface;
use Gram\Route\Route;

abstract class Generator implements GeneratorInterface
{
	const CHUNKSIZE = 10;

	private $dynamic=[];
	private $static=[];

	public function generate(array $routes){
		foreach ($routes as $i=>$route) {
			$this->mapRoute($route);
		}

		$this->dynamic=$this->generateDynamic($this->dynamic);	//Genereire Dynamic Routemap

		return ['static'=>$this->static,'dynamic'=>$this->dynamic];
	}

	private function mapRoute(Route $route){
		$route->createRoute();	//parse die Route

		//Ordne die Route in Static und Dynamic
		if (count($route->vars)===0){
			$this->static[$route->path]=$route->handle;
		}else{
			$this->dynamic[]=$route;
		}
	}

}