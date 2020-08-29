<?php
/**
 * phpgram
 *
 * This File is part of the phpgram Micro Framework
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

namespace Gram\Route\Interfaces;

/**
 * Interface Dispatcher
 * @package Gram\Route\Dispatcher
 *
 * Ein Interface das alle Dispatcher implementieren müssen
 */
interface DispatcherInterface
{
	const FOUND = 200;
	const NOT_FOUND = 404;
	const NOT_ALLOWED = 405;

	/**
	 * Pürfe ob die Route in dem Array ist (static Route)
	 *
	 * Sonst führe den Dynamischen Dispatcher aus
	 *
	 * @param $method
	 * @param $uri
	 * @return array
	 */
	public function dispatch($method,$uri);
}