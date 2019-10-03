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
 * Interface StrategyCollectorInterface
 * @package Gram\Route\Interfaces
 *
 * Ein Interface für alle Strategy Sammler
 */
interface StrategyCollectorInterface
{
	public function addStd($strategy);
	public function addRoute($routeid,$strategy);
	public function addGroup($groupid,$strategy);
	public function getStd();
	public function getGroup($id);
	public function getRoute($id);
}