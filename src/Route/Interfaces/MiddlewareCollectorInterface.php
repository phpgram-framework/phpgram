<?php
namespace Gram\Route\Interfaces;


interface MiddlewareCollectorInterface
{
	public function addStd($middleware,$order=null);
	public function addRoute($routeid,$middleware,$order=null);
	public function addGroup($groupid,$middleware,$order=null);
	public function getStdMiddleware();
	public function getGroup($id);
	public function getRoute($id);
}