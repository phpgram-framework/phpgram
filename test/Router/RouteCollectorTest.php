<?php
namespace Gram\Test\Router;
use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Collector\RouteCollector;
use Gram\Route\Collector\StrategyCollector;
use Gram\Route\Generator\DynamicGenerator;
use Gram\Route\Parser\StdParser;
use PHPUnit\Framework\TestCase;

class RouteCollectorTest extends TestCase
{
	public function testRouteCollector(){
		$collector = new RouteCollector(
			new StdParser(),
			new DynamicGenerator(),
			new MiddlewareCollector(),
			new StrategyCollector()
		);
	}
	
	public function testRouteCollectorCache(){
		$collector = new RouteCollector(
			new StdParser(),
			new DynamicGenerator(),
			new MiddlewareCollector(),
			new StrategyCollector(),
			true,
			"hallo.cache"
		);
	}
}