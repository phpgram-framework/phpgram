<?php
/**
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 * @since 2020/08/29
 */

namespace Gram\Test\TestClasses;

use Gram\Middleware\Classes\ClassInterface;
use Gram\Middleware\Classes\ClassTrait;

abstract class TestClassAbstractClassForResolver implements ClassInterface
{
	use ClassTrait;

	public function doIt()
	{

	}
}