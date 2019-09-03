<?php
namespace Gram\Callback;
use Psr\Http\Message\ServerRequestInterface;

interface Callback
{
	public function callback($param=[],ServerRequestInterface $request);

	public function set();
}