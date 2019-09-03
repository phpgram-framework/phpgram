<?php
namespace Gram\Strategy;
use Gram\Callback\Callback;
use Psr\Http\Message\ServerRequestInterface;

interface StrategyInterface
{
	public function getHeader();
	public function invoke(Callback $callback,array $param,ServerRequestInterface $request);
}