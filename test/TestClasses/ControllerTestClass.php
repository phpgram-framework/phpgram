<?php
namespace Gram\Test\TestClasses;

use Gram\Middleware\Classes\ClassInterface;
use Gram\Middleware\Classes\ClassTrait;

/**
 * Class ControllerTestClass
 * @package Gram\Test\TestClasses
 *
 * Test Class für den App Test
 */
class ControllerTestClass implements ClassInterface
{
	use ClassTrait;

	public static $staticCounter = 0;

	public $instanceCounter = 0;

	public function index()
	{
		return "hallo";
	}

	public function getSomeValue($value)
	{
		return "value: $value";
	}

	/**
	 * @throws \Exception
	 */
	public function exception()
	{
		throw new \Exception('');
	}

	public function buffered()
	{
		echo "buffer Test";
	}

	public function json()
	{
		return ["value1","value2","value3"];
	}

	public function returnResponse()
	{
		$this->response->getBody()->write('hello');

		return $this->response;
	}

	public function testAsync()
	{
		$this->instanceCounter++;

		self::$staticCounter += $this->instanceCounter;

		return self::$staticCounter.$this->instanceCounter;
	}
}