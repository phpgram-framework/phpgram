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
 * Interface QueueInterface
 * @package Gram\Middleware\Queue
 *
 * Ein Interface für alle Middleware Queues
 *
 * Wird für jeden Request neu erstellt
 */
interface QueueInterface
{
	/**
	 * Fügt eine Middleware der Queue hinzu
	 *
	 * @param $middleware
	 * @return void
	 */
	public function add($middleware);

	/**
	 * Gibt die nächste Middleware zurück die für diesen Request dran ist
	 *
	 * @return mixed|bool
	 * Gibt false zurück wenn der Stack leer ist
	 * Sonst die Middleware
	 */
	public function next();
}