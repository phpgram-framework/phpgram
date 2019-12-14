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
}