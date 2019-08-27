<?php
namespace Gram\Handler;

/**
 * Class CallbackHandler
 * @package Gram\Handler
 * @author Jörn Heinemann
 * Speichert ein Callable und gibt es wieder zurück
 */
class CallbackHandler implements Handler
{
	protected $callback;

	/**
	 * Führe das Callback aus
	 * @param array $param
	 * @param $request
	 * @return mixed
	 */
	public function callback($param=array(),$request){
		$param[]=$request;	//letzter parameter ist immer der request bei functions
		return call_user_func_array($this->callback,$param);
	}

	/**
	 * @param null $callback
	 * @throws \Exception
	 */
	public function set($callback=null){
		if(!is_callable($callback)){
			throw new \Exception("Kein Callable!");
		}

		$this->callback=$callback;
	}
}