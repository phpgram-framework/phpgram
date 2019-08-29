<?php
namespace Gram\Callback;


interface Callback
{
	public function callback($param=array(),$request);

	public function set();
}