<?php
namespace Gram\Route\Interfaces;


interface StrategyCollectorInterface
{
	public function addRoute($routeid,$strategy);
	public function addGroup($groupid,$strategy);
	public function getGroup();
	public function getRoute();
}