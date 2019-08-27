<?php
namespace Gram\Route\Generator;
use Gram\Route\Route;

class StaticGenerator implements Generator
{
	private $staticroutes=array();

	public function generate(array $routes){
		foreach ($routes as $i=>$route) {
			$this->mapRoute($route);
		}

		return array(
			'staticroutes'=>$this->staticroutes
		);
	}

	private function mapRoute(Route $route){
		$this->staticroutes[$route->path]=$route->handle;
	}
}