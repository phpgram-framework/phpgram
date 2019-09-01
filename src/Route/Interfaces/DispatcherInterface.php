<?php
namespace Gram\Route\Interfaces;
/**
 * Interface Dispatcher
 * @package Gram\Route\Dispatcher
 * @author Jörn Heinemann
 * Ein Interface das alle Dispatcher implementieren müssen
 */
interface DispatcherInterface
{
	const FOUND=1;
	const NOT_FOUND = 0;

	public function setData(array $routes);
	public function dispatch($uri);
	public function dispatchDynamic($uri, array $routes,array $handler);
}