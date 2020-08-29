<?php
/**
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 * @since 2020/08/29
 */

namespace Gram\Test\TestClasses;

use Gram\Middleware\Classes\ClassInterface;
use Gram\Middleware\Classes\ClassTrait;

class TestClassWithDefaultDependency implements ClassInterface
{
	use ClassTrait;

	/**
	 * @var TestClass | null
	 */
	private $id;

	public function __construct(TestClass $id = null)
	{
		$this->id = $id;
	}

	public function doing()
	{
		return $this->id;
	}
}