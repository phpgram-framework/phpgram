<?php
namespace Gram\Route\Interfaces\Components;
use Gram\Route\Interfaces\Collector;

interface MiddlewareCollector extends Collector
{
	public function add($route,array $stack,$atFirst=false);
	public function addStd(array $stack);
}