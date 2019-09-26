<?php
namespace Gram\Test\Router;


class RouteMap
{
	public function map()
	{
		return [
			'/test/vars/{var:n}/tester'
		];
	}

	public function handler()
	{
		return [
			"hallo"
		];
	}
}