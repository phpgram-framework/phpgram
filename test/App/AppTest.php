<?php
namespace Gram\Test\App;

use Gram\App\App;
use Gram\Test\TestClasses\ControllerTestClass;

class AppTest extends AbstractAppTest
{
	protected function getApp():App
	{
		return new App();
	}

	public function testAsyncRequests()
	{
		$app = $this->initApp();

		$request = $this->request;

		$app->build();

		//teste 100 requests mit der selben instance
		for($i = 0; $i < 100; $i++) {
			$uri = $this->psr17->createUri('https://jo.com/test/vars/123@/async');

			$request = $request->withUri($uri);

			$response = $app->handle($request);

			$body = $response->getBody()->__toString();
			$status = $response->getStatusCode();

			$j = $i+1;

			//instance Varaiblen werden immer erneuert, static nicht
			//teste zudem die Werte die die Mws in den Request packen
			self::assertEquals($j."1 Mws: 123",$body);
			self::assertEquals(200,$status);
		}

		self::assertEquals('100',ControllerTestClass::$staticCounter);
	}

	public function testAsyncRequestsWithDi()
	{
		$app = $this->initApp();

		$request = $this->request;

		$app->build();

		//teste 100 requests mit der selben instance
		for($i = 0; $i < 100; $i++) {
			$uri = $this->psr17->createUri('https://jo.com/test/vars/123a/tester');

			$request = $request->withUri($uri);

			$response = $app->handle($request);

			$body = $response->getBody()->__toString();
			$status = $response->getStatusCode();

			self::assertEquals('Gram\Test\TestClasses\TestClass right Testresult',$body);
			self::assertEquals(200,$status);
		}
	}
}