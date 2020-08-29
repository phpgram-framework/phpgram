<?php
namespace Gram\Test\Router;

use Gram\Route\Router;
use Nyholm\Psr7\Factory\Psr17Factory;

class GroupCountBasedTest extends TestRoutes
{
	protected function getRouterOptions(): array
	{
		return [
			'dispatcher'=>'Gram\\Route\\Dispatcher\\GroupCountBased',
			'generator'=>'Gram\\Route\\Generator\\GroupCountBased'
		];
	}
}