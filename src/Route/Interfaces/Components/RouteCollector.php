<?php
namespace Gram\Route\Interfaces\Components;
use Gram\Route\Interfaces\Collector;

interface RouteCollector extends Collector
{
	public function notFound($controller,$function="");
	public function notAllowed($controller,$function="");
	public function add($route,$controller,$routingTyp,$method='get');
}