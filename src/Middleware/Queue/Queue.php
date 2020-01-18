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

namespace Gram\Middleware\Queue;

/**
 * Class Queue
 * @package Gram\Middleware\Queue
 *
 * Eine einfache Queue für Middleware
 */
class Queue implements QueueInterface
{
	private $stack = [];

	/**
	 * @inheritdoc
	 */
	public function add($middleware)
	{
		$this->stack[] = $middleware;
	}

	/**
	 * @inheritdoc
	 */
	public function next()
	{
		return \array_shift($this->stack) ?? false;
	}
}