<?php
namespace Gram\Test\Router;


class RouteMap
{
	public function map()
	{
		return [
			'/test/vars/{var:n}/tester',
			'/test/vars/{var:a}/tester',
			'/test/vars/{var}/tester'
		];
	}

	public function handler()
	{
		return [
			"TestHandler0",
			"TestHandler1",
			"TestHandler2"
		];
	}
}