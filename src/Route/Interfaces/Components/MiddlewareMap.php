<?php
namespace Gram\Route\Interfaces\Components;
use Gram\Route\Interfaces\Map;

interface MiddlewareMap extends Map
{
	public function getStd();
}