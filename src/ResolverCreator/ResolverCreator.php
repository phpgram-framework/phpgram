<?php
/**
 * phpgram
 *
 * This File is part of the phpgram Micro Framework
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

namespace Gram\ResolverCreator;

use Gram\Exceptions\CallableNotAllowedException;
use Gram\Resolver\CallableResolver;
use Gram\Resolver\ClassResolver;
use Gram\Resolver\ClosureResolver;
use Gram\Resolver\HandlerResolver;
use Gram\Middleware\Handler\HandlerInterface;

/**
 * Class CallableCreator
 * @package Gram\CallbackCreator
 *
 * Erstellt ein Callable aus etwas übergebenem
 */
class ResolverCreator implements ResolverCreatorInterface
{
	/**
	 * @inheritdoc
	 *
	 * Prüft ob das callable ein String, HandlerInterface oder Callable ist
	 *
	 * Erstellt dann den entsprechenden Resolver
	 *
	 * @throws \Exception
	 */
	public function createResolver($possibleCallable)
	{
		if(\is_string($possibleCallable)) {
			return $this->createCallbackForClass($possibleCallable);
		}

		if($possibleCallable instanceof HandlerInterface){
			return $this->createHandlerCallback($possibleCallable);
		}

		if($possibleCallable instanceof \Closure){
			return $this->createCallbackFromClosure($possibleCallable);
		}

		if(\is_callable($possibleCallable)) {
			return $this->createCallbackFromCallable($possibleCallable);
		}

		throw new CallableNotAllowedException("Pattern doesn't match!");
	}

	/**
	 * @param $class
	 * @return ClassResolver
	 * @throws \Exception
	 */
	private function createCallbackForClass($class)
	{
		$callback = new ClassResolver();
		$callback->set($class);

		return $callback;
	}

	/**
	 * @param \Closure $callable
	 * @return ClosureResolver
	 * @throws \Exception
	 */
	private function createCallbackFromClosure(\Closure $callable)
	{
		$callback= new ClosureResolver();
		$callback->set($callable);

		return $callback;
	}

	/**
	 * @param HandlerInterface $handler
	 * @return HandlerResolver
	 * @throws \Exception
	 */
	private function createHandlerCallback(HandlerInterface $handler)
	{
		$callback = new HandlerResolver();
		$callback->set($handler);

		return $callback;
	}

	/**
	 * @param callable $callable
	 * @return CallableResolver
	 * @throws \Gram\Exceptions\CallableNotAllowedException
	 */
	private function createCallbackFromCallable(callable $callable)
	{
		$callback = new CallableResolver();
		$callback->set($callable);

		return $callback;
	}
}