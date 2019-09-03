<?php
namespace Gram\Callback;

use Gram\Middleware\Handler\HandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class HandlerCallback implements Callback
{
	private $handler;

	public function callback($param = [],ServerRequestInterface $request)
	{
		$return= call_user_func_array([$this->handler,'handle'],[$request]);

		return ($return===null)?'':$return;	//default: immer einen String zurück geben
	}

	/**
	 * @param HandlerInterface|null $handler
	 * @throws \Exception
	 */
	public function set(HandlerInterface $handler=null)
	{
		if($handler===null){
			throw new \Exception("Keinen Handler angegeben");
		}

		$this->handler=$handler;
	}
}