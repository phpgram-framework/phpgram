<?php
namespace Gram\Test\App;

use Gram\App\App;
use Gram\Test\TestClasses\ControllerTestClass;

/**
 * Class AsyncAppTest
 * @package Gram\Test\App
 *
 * Test für die Async App Klasse
 */
class AsyncAppTest extends AbstractAppTest
{
	protected function getApp():App
	{
		return new AsyncAppTestInit();
	}

	protected function setUp(): void
	{
		$this->initApp();
	}

	public function testAsyncRequests()
	{
		$request = $this->request;

		//teste 100 requests mit der selben instance
		for($i = 0; $i < 100; $i++) {
			$uri = $this->psr17->createUri('https://jo.com/test/vars/123@/async');

			$request = $request->withUri($uri);

			$response = $this->app->handle($request);

			$body = $response->getBody()->__toString();
			$status = $response->getStatusCode();

			$j = $i+1;

			//instance Varaiblen werden immer erneuert, static nicht
			self::assertEquals($j."1",$body);
			self::assertEquals(200,$status);
		}

		self::assertEquals('100',ControllerTestClass::$staticCounter);
	}

	public function testAsyncRequestsWithDi()
	{
		$request = $this->request;

		//teste 100 requests mit der selben instance
		for($i = 0; $i < 100; $i++) {
			$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

			$request = $request->withUri($uri);

			$response = $this->app->handle($request);

			$body = $response->getBody()->__toString();
			$status = $response->getStatusCode();

			self::assertEquals('Gram\Test\TestClasses\TestClass right Testresult',$body);
			self::assertEquals(200,$status);
		}
	}
}