<?php
namespace Gram\Test\Router;

use Gram\Route\Router;
use Nyholm\Psr7\Factory\Psr17Factory;

class MarkBasedTest extends TestRoutes
{
	protected function getRouterOptions(): array
	{
		return [
			'dispatcher'=>'Gram\\Route\\Dispatcher\\MarkBased',
			'generator'=>'Gram\\Route\\Generator\\MarkBased'
		];
	}
}