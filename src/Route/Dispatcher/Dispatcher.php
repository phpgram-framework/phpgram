<?php
namespace Gram\Route\Dispatcher;

/**
 * Interface Dispatcher
 * @package Gram\Route\Dispatcher
 * @author Jörn Heinemann
 * Ein Interface das alle Dispatcher implementieren müssen
 */
interface Dispatcher
{
	const FOUND=1;
	const NOT_FOUND = 0;

	public function dispatch($uri);
}