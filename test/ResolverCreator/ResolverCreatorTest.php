<?php
namespace Gram\Test\ResolverCreator;

use Gram\Resolver\CallbackResolver;
use Gram\Resolver\ClassResolver;
use Gram\ResolverCreator\ResolverCreator;
use Gram\Test\TestClasses\TestClass;
use PHPUnit\Framework\TestCase;

class ResolverCreatorTest extends TestCase
{
	/** @var ResolverCreator */
	private $creator;

	protected function setUp(): void
	{
		$this->creator = new ResolverCreator();
	}

	public function testClassResolverCreation()
	{
		$toResolve = TestClass::class."@doSmth";

		try{
			$this->creator->createResolver($toResolve);
			$resolver = $this->creator->getResolver();
		}catch (\Exception $e){
			echo $e;
			$resolver = null;
		}

		self::assertInstanceOf(ClassResolver::class,$resolver);
	}

	public function testFunctionResolverCreation()
	{
		$toResolve = function (){
			return "Test";
		};

		try{
			$this->creator->createResolver($toResolve);
			$resolver = $this->creator->getResolver();
		}catch (\Exception $e){
			echo $e;
			$resolver = null;
		}

		self::assertInstanceOf(CallbackResolver::class,$resolver);
	}
}