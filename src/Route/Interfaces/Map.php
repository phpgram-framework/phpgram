<?php
namespace Gram\Route\Interfaces;

interface Map
{
	public function initMap();
	public function getMap();
	public function getValue(string $key);
}