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

namespace Gram\Middleware\Handler;

use Gram\Middleware\Classes\ClassInterface;

/**
 * Interface HandlerInterface
 * @package Gram\Middleware\Handler
 *
 * Ein Interface für Handler die von Middleware ausgeführt werden können
 *
 * Wird als Object ausgeführt
 *
 * Hat die gleichen Attribute wie Klassen als Strings
 */
interface HandlerInterface extends ClassInterface
{
	/**
	 * Method die vom Resolver ausgeführt wird
	 *
	 * @return mixed
	 */
	public function handle();
}