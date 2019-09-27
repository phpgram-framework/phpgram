<?php
namespace Gram\Test\TestClasses;

use Gram\Middleware\Classes\ClassInterface;
use Gram\Middleware\Classes\ClassTrait;

class TestClassDi implements ClassInterface
{
	use ClassTrait;

	private $testclass;

	public function __construct(TestClass $testClass)
	{
		$this->testclass = $testClass;
	}

	public function testDi()
	{
		return $this->testclass->doSmth();
	}
}