<?php
namespace Gram\App;


use Gram\Callback\Callback;
use Gram\Callback\CallbackCallback;
use Gram\Callback\ClassCallback;
use Gram\Callback\ControllerCallback;
use Gram\Callback\MiddlewareCallback;

class CallableCreator
{
	private $callable=null;

	/**
	 * CallableCreator constructor.
	 * Prüft ob etwas ein Callable bzw ein Stack mit Callable ist
	 * Stack sind Middlewares, die einzelnen Elemente ztu Callable umformen
	 * Normales Callable prüfen ob es aus einem Array besteht (class und function) -> classhandler erstellen
	 * sonst etweder ein ControllerHandler erstellen (ein Art ClassHandler) oder
	 * wenn es eine Function war im Callable handler speichern
	 * @param $possibleCallable
	 */
	public function __construct($possibleCallable){
		if(!is_array($possibleCallable)){
			$this->callable=$this->createCallback($possibleCallable);
		}else{
			$this->callable=$this->createCallbackForClass($possibleCallable[0],$possibleCallable[1]);
		}
	}

	private function createCallbackForMVC($controller){
		$callback = new ControllerCallback();
		try{
			$callback->setC($controller);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}

	private function createCallbackForClass($class,$function){
		$callback = new ClassCallback();
		try{
			$callback->set($class,$function);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}

	private function createCallbackFromCallable(callable $callable){
		$callback= new CallbackCallback();
		try{
			$callback->set($callable);
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
	protected function createCallback($callback){
		if(is_callable($callback)){
			return $this->createCallbackFromCallable($callback);
		}else{
			return $this->createCallbackForMVC($callback);
		}
	}


	/**
	 * @return bool|Callback|ClassCallback|ControllerCallback|MiddlewareCallback|CallbackCallback
	 */
	public function getCallable(){
		return $this->callable;
	}
}