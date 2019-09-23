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
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\ResolverCreator;

use Gram\Resolver\ResolverInterface;
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
	protected $callable=null;

	/**
	 * @inheritdoc
	 *
	 * Prüft ob etwas ein Callable bzw ein Stack mit Callable ist
	 *
	 * Unterscheidet zwischen Handler (Handlerobjekt), Class und Function
	 *
	 * Normales Callable prüfen ob es aus einem Array besteht (class und function) -> classhandler erstellen
	 *
	 * sonst etweder ein ControllerHandler erstellen (ein Art ClassHandler) oder
	 *
	 * wenn es eine Function war im Callable handler speichern
	 *
	 * @param $possibleCallable
	 */
	public function createResolver($possibleCallable)
	{
		if(is_object($possibleCallable) && $possibleCallable instanceof HandlerInterface){
			$this->callable=$this->createHandlerCallback($possibleCallable);
		}else{
			$this->callable=$this->createCallbackFor($possibleCallable);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getCallable():ResolverInterface
	{
		return $this->callable;
	}

	private function createCallbackForClass($class)
	{
		$callback = new ClassResolver();
		try{
			$callback->set($class);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}

	private function createCallbackFromCallable(callable $callable)
	{
		$callback= new CallbackResolver();
		try{
			$callback->set($callable);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}

	private function createHandlerCallback(HandlerInterface $handler)
	{
		$callback = new HandlerResolver();
		try{
			$callback->set($handler);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}

	/**
	 * Prüfe ob der Controller callable ist,
	 * wenn ja benutze den Callable Handler und nicht den Controller Handler
	 * @param $callback
	 * @return bool|CallbackResolver|ClassResolver
	 */
	private function createCallbackFor($callback)
	{
		if(is_callable($callback)){
			return $this->createCallbackFromCallable($callback);
		}else{
			return $this->createCallbackForClass($callback);
		}
	}
}