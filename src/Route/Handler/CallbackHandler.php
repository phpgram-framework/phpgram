<?php
namespace Gram\Route\Handler;


class CallbackHandler extends Handler
{
	protected $callback;

	public function callback(){
		return $this->callback;
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

	public static function __set_state($vars){
		$handler = new self();
		$handler->callback=$vars['callback'];

		return $handler;
	}
}