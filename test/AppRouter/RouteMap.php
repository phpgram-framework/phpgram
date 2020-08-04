<?php
namespace Gram\Test\AppRouter;

use Gram\Test\TestClasses\ControllerTestClass;
use Gram\Test\TestClasses\TestClassDi;

class RouteMap
{
	public function map()
	{
		return [
			'/test/vars/{var:n}/tester',
			'/test/vars/{var:a}/tester',
			'/test/vars/{var}/tester',
			'/',
			'/abc',
			'/exception',
			'/test/vars/{var}/async',
			'/test/optional[/{id}[/{id1}]]'
		];
	}

	public function handler()
	{
		return [
			"TestHandler0",
			"TestHandler1",
			"TestHandler2",
			"Start",
			"abc",
			"exception",
			"async",
			"optional"
		];
	}

	public function realHandler()
	{
		return [
			ControllerTestClass::class."@getSomeValue",
			TestClassDi::class."@testDi",
			ControllerTestClass::class."@getSomeValue",
			ControllerTestClass::class."@index",
			"abc",
			ControllerTestClass::class."@exception",
			ControllerTestClass::class."@testAsync",
			"optional"
		];
	}
}