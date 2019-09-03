<?php
namespace Gram\Strategy;

use Gram\Callback\Callback;
use Psr\Http\Message\ServerRequestInterface;

class StdAppStrategy implements StrategyInterface
{
	public function getHeader()
	{
		return ["name"=>'Content-Type',"value"=>'text/html'];
	}

	public function invoke(Callback $callback, array $param, ServerRequestInterface $request)
	{
		return $callback->callback($param,$request);
	}
}