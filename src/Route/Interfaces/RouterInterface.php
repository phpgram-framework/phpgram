<?php
namespace Gram\Route\Interfaces;


interface RouterInterface
{
	public function run($uri,$httpMethod=null);
	public function getStatus();
	public function getHandle();
	public function getParam();
	public function getCollector();
}