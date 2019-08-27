<?php
namespace Gram\Handler;


interface Handler
{
	public function callback($param=array(),$request);

	public function set();
}