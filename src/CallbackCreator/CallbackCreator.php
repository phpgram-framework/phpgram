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

namespace Gram\CallbackCreator;

use Gram\Callback\CallbackInterface;
use Gram\Callback\CallbackCallback;
use Gram\Callback\ClassCallback;
use Gram\Callback\ControllerCallback;
use Gram\Callback\HandlerCallback;
use Gram\Middleware\Handler\HandlerInterface;

/**
 * Class CallableCreator
 * @package Gram\CallbackCreator
 *
 * Erstellt ein Callable aus etwas übergebenem
 */
class CallbackCreator implements CallbackCreatorInterface
{
	protected $callable=null;

	/**
	 * @inheritdoc
	 *
	 * Prüft ob etwas ein Callable bzw ein Stack mit Callable ist
	 *
	 * Unterscheidet zwischen Handler (Handlerobjekt), Class und Function, Controller oder Function
	 *
	 * Normales Callable prüfen ob es aus einem Array besteht (class und function) -> classhandler erstellen
	 *
	 * sonst etweder ein ControllerHandler erstellen (ein Art ClassHandler) oder
	 *
	 * wenn es eine Function war im Callable handler speichern
	 *
	 * @param $possibleCallable
	 */
	public function createCallback($possibleCallable)
	{
		if(is_object($possibleCallable) && $possibleCallable instanceof HandlerInterface){
			$this->callable=$this->createHandlerCallback($possibleCallable);
		}else if(!is_array($possibleCallable)){
			$this->callable=$this->createCallbackFor($possibleCallable);
		}else{
			$this->callable=$this->createCallbackForClass($possibleCallable[0],$possibleCallable[1]);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getCallable():CallbackInterface
	{
		return $this->callable;
	}

	private function createCallbackForMVC($controller)
	{
		$callback = new ControllerCallback();
		try{
			$callback->setC($controller);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}

	private function createCallbackForClass($class,$function)
	{
		$callback = new ClassCallback();
		try{
			$callback->set($class,$function);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}

	private function createCallbackFromCallable(callable $callable)
	{
		$callback= new CallbackCallback();
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
		$callback = new HandlerCallback();
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
	 * @return bool|CallbackCallback|ControllerCallback
	 */
	private function createCallbackFor($callback)
	{
		if(is_callable($callback)){
			return $this->createCallbackFromCallable($callback);
		}else{
			return $this->createCallbackForMVC($callback);
		}
	}
}