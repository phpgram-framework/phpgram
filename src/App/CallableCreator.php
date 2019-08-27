<?php
namespace Gram\App;
use Gram\Handler\ControllerHandler;
use Gram\Handler\ClassHandler;
use Gram\Handler\CallbackHandler;

class CallableCreator
{
	private $callable=null;

	public function __construct($possibleCallable,$stack=array()){
		if(!empty($stack)){
			//Middleware Stack zu Callables machen
			$this->callable=$this->createCallbackFromStack($stack);
		}else{
			if(!is_array($possibleCallable)){
				$this->callable=$this->createCallback($possibleCallable);
			}else{
				$this->callable=$this->createCallbackForClass($possibleCallable[0],$possibleCallable[1]);
			}
		}
	}

	private function createCallbackFromStack(array $stack){
		return array_map(array($this,'createCallbackFromStack_helper'),$stack);
	}

	private function createCallbackFromStack_helper($possibleCallable){
		$creator = new self($possibleCallable);
		return $creator->getCallable();
	}

	private function createCallbackForMVC($controller){
		$callback = new ControllerHandler();
		try{
			$callback->setC($controller);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}

	private function createCallbackForClass($class,$function){
		$callback = new ClassHandler();
		try{
			$callback->set($class,$function);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}

	private function createCallbackFromCallable(callable $callable){
		$callback= new CallbackHandler();
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
	 * @return bool|CallbackHandler|ControllerHandler
	 */
	protected function createCallback($callback){
		if(is_callable($callback)){
			return $this->createCallbackFromCallable($callback);
		}else{
			return $this->createCallbackForMVC($callback);
		}
	}

	/**
	 * @return bool|ClassHandler|null
	 */
	public function getCallable(){
		return $this->callable;
	}
}