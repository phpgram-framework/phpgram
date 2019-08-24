<?php
namespace Gram\Route\Generator;


class StaticGenerator
{
	public function generate(array $routes){
		$staticroutes=array();
		$statichandler=array();

		foreach ($routes as $i=>$route) {
			$staticroutes[$i]=$route['route'];
			$statichandler[$i]=$route['handle'];
		}

		return array(
			'staticroutes'=>$staticroutes,
			'statichandler'=>$statichandler
		);
	}
}