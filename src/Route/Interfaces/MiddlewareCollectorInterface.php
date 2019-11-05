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
 * Interface MiddlewareCollectorInterface
 * @package Gram\Route\Interfaces
 *
 * Ein interface für alle Middleware Sammler
 */
interface MiddlewareCollectorInterface
{
	public function addStd($middleware);
	public function addRoute($routeid,$middleware);
	public function addGroup($groupid,$middleware);
	public function getStdMiddleware();
	public function getGroup($id);
	public function getRoute($id);
}