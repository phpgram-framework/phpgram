<?php
namespace Gram\Test\ResolverCreator;

use Gram\Resolver\CallableResolver;
use Gram\Resolver\ClassResolver;
use Gram\Resolver\ClosureResolver;
use Gram\Resolver\HandlerResolver;
use Gram\ResolverCreator\ResolverCreator;
use Gram\Test\TestClasses\CallableClass;
use Gram\Test\TestClasses\TestClass;
use Gram\Test\TestClasses\TestHandlerClass;
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
			$resolver = $this->creator->createResolver($toResolve);
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
			$resolver = $this->creator->createResolver($toResolve);
		}catch (\Exception $e){
			echo $e;
			$resolver = null;
		}

		self::assertInstanceOf(ClosureResolver::class,$resolver);
	}

	public function testCallableResolverCreation()
	{
		$toResolve = new CallableClass();

		try{
			$resolver = $this->creator->createResolver($toResolve);
		}catch (\Exception $e){
			echo $e;
			$resolver = null;
		}

		self::assertInstanceOf(CallableResolver::class,$resolver);
	}

	public function testHandlerResolverCreation()
	{
		$toResolve = new TestHandlerClass();

		try{
			$resolver = $this->creator->createResolver($toResolve);
		}catch (\Exception $e){
			echo $e;
			$resolver = null;
		}

		self::assertInstanceOf(HandlerResolver::class,$resolver);
	}

	public function testNotResolver()
	{
		$toResolve = new TestClass();

		try{
			$resolver = $this->creator->createResolver($toResolve);
		}catch (\Exception $e){
			//echo $e;
			$resolver = null;
		}

		self::assertEquals(null,$resolver);
	}
}