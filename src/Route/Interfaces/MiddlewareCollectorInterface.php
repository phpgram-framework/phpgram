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
 * @author Jörn Heinemann <j.heinemann1@web.de>
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
	public function addStd($middleware,$order=null);
	public function addRoute($routeid,$middleware,$order=null);
	public function addGroup($groupid,$middleware,$order=null);
	public function getStdMiddleware();
	public function getGroup($id);
	public function getRoute($id);
}