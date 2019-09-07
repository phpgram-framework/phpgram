<?php
namespace Gram\Test\Router;
use Gram\Route\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
	public function testSimpleRoutes(){
		$router= new Router();

		$collector= $router->getCollector();

		$factory= new Psr17Factory();

		$uri = $factory->createUri('https://jo.com/test/vars/123/tester');

		$collector->get('/test/vars/{var:n}/tester',"hallo");

		$router->run($uri->getPath());

		$handler = $router->getHandle();

		self::assertEquals('hallo',$handler['callable']);
	}
}