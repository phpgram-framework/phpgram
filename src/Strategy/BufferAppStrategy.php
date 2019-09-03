<?php
namespace Gram\Strategy;

use Gram\Callback\Callback;
use Psr\Http\Message\ServerRequestInterface;

class BufferAppStrategy extends StdAppStrategy
{

	public function invoke(Callback $callback, array $param, ServerRequestInterface $request)
	{
		ob_start();
		parent::invoke($callback,$param,$request);
		$return=ob_get_clean();
		ob_flush();

		return $return;
	}
}