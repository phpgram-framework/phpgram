<?php
namespace Gram\Callback;


class MiddlewareCallback implements Callback
{
	private $middleware;

	public function set($middleware=""){
		$this->middleware=$middleware;
	}

	public function callback($param = array(), $request=""){
		return new $this->middleware;
	}
}