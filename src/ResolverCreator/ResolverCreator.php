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

use Gram\Resolver\CallbackResolver;
use Gram\Resolver\ClassResolver;
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
		if(\is_object($possibleCallable) && $possibleCallable instanceof HandlerInterface){
			return $this->createHandlerCallback($possibleCallable);
		}

		if(\is_callable($possibleCallable)){
			return $this->createCallbackFromCallable($possibleCallable);
		}

		return $this->createCallbackForClass($possibleCallable);
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
	 * @param callable $callable
	 * @return CallbackResolver
	 * @throws \Exception
	 */
	private function createCallbackFromCallable(callable $callable)
	{
		$callback= new CallbackResolver();
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
}