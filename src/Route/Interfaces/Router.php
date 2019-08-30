<?php
namespace Gram\Route\Interfaces;


interface Router
{
	public function run($uri,$httpMethod=null);
	public function getStatus();
	public function getHandle();
	public function getParam();
	public function getMap();
}