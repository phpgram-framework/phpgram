<?php
namespace Gram\Test\ResolverCreator;

use Gram\Resolver\CallableResolver;
use Gram\Resolver\ClassResolver;
use Gram\Resolver\ClosureResolver;
use Gram\ResolverCreator\ResolverCreator;
use Gram\Test\TestClasses\CallableClass;
use Gram\Test\TestClasses\TestClass;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Gram\ResolverCreator\ResolverCreator::createCallbackForClass()
 * @covers \Gram\ResolverCreator\ResolverCreator::createCallbackFromCallable()
 * @covers \Gram\ResolverCreator\ResolverCreator::createCallbackFromClosure()
 * @covers \Gram\ResolverCreator\ResolverCreator::createHandlerCallback()
 * @covers \Gram\ResolverCreator\ResolverCreator::createHandlerCallback()
 * @uses ResolverCreator
 */
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
}