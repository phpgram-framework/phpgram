<?php
namespace Gram\Route\Interfaces;


interface StrategyCollectorInterface
{
	public function addStd($strategy);
	public function addRoute($routeid,$strategy);
	public function addGroup($groupid,$strategy);
	public function getStd();
	public function getGroup($id);
	public function getRoute($id);
}