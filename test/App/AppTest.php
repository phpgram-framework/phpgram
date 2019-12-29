<?php
namespace Gram\Test\App;

use Gram\App\App;
use Gram\Test\Middleware\DummyMw\TestMw1;
use Gram\Test\Router\RouteMap;
use Gram\Test\TestClasses\TestClass;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Pimple\Container;

/**
 * Class AppTest
 * @package Gram\Test\App
 *
 * Test für die normale App Klasse
 */
class AppTest extends AbstractAppTest
{
	protected function getApp():App
	{
		return new AppTestInit();
	}

	protected function setUp(): void
	{
		$this->initApp();

		$this->app->init();
	}

}