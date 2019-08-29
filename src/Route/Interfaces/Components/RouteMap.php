<?php
namespace Gram\Route\Interfaces\Components;
use Gram\Route\Interfaces\Map;

interface RouteMap extends Map
{
	public function get404();
	public function get405();
}