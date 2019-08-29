<?php
namespace Gram\Route\Generator;
use Gram\Route\Route;

class StaticGenerator implements \Gram\Route\Interfaces\Components\StaticGenerator
{
	private $staticroutes=array();

	public function generate(array $routes){
		foreach ($routes as $i=>$route) {
			$this->mapRoute($route);
		}

		$return = array(
			'staticroutes'=>$this->staticroutes
		);

		$this->staticroutes=array();	//zurück setzen

		return $return;
	}

	private function mapRoute(Route $route){
		$this->staticroutes[$route->path]=$route->handle;
	}
}