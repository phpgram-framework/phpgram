<?php
namespace Gram\Route\Handler;


abstract class Handler
{
	abstract public function callback();

	abstract public function set();
}