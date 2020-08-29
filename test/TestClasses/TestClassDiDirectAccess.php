<?php
/**
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 * @since 2020/08/29
 */

namespace Gram\Test\TestClasses;

use Gram\Middleware\Classes\ClassInterface;
use Gram\Middleware\Classes\ClassTrait;

class TestClassDiDirectAccess implements ClassInterface
{
	use ClassTrait;

	public function getAccess()
	{
		/** @var TestClass $testClass */
		$testClass = $this->{TestClass::class};

		if(!isset($testClass)) {
			return null;
		}

		return $testClass->doSmth();
	}
}