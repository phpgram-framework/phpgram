<?php
namespace Gram\Test\TestClasses;

use Gram\Middleware\Classes\ClassInterface;
use Gram\Middleware\Classes\ClassTrait;

class TestClass implements ClassInterface
{
	use ClassTrait;

	public function doSmth()
	{
		return TestClass::class." right Testresult";
	}

	public function doPsr()
	{
		$this->response = $this->response->withStatus(404);

		$callTest = $this->request->getAttribute('testCall');

		return $callTest;
	}

	public function testWithParam($param1,$param2,$param3)
	{
		$string1= implode(" ",$param1);

		return "$string1 $param2 $param3";
	}
}