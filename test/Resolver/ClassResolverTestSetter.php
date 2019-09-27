<?php
namespace Gram\Test\Resolver;

use Gram\Resolver\ClassResolver;

class ClassResolverTestSetter extends ClassResolver
{
	public function getClassName()
	{
		return $this->classname;
	}

	public function getFunction()
	{
		return $this->function;
	}
}