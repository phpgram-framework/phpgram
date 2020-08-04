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

namespace Gram\App\Route;

/**
 * Interface StrategyCollectorInterface
 * @package Gram\Route\Interfaces
 *
 * Ein Interface für alle Strategy Sammler
 */
interface StrategyCollectorInterface
{
	/**
	 * Füge eine Strategy für eine bestimmte Route hinzu
	 *
	 * Es wird immer nur die zu letzt hinzugefügte genommen!
	 *
	 * @param $routeid
	 * Für Welche Route
	 *
	 * @param $strategy
	 * @return void
	 */
	public function addRoute($routeid,$strategy);

	/**
	 * Füge eine Strategy für eine bestimmte Gruppe hinzu
	 *
	 * Es wird immer nur die zu letzt hinzugefügte genommen!
	 *
	 * @param $groupid
	 * @param $strategy
	 * @return void
	 */
	public function addGroup($groupid,$strategy);

	/**
	 * Gebe die Strategy für eine Gruppe zurück
	 *
	 * @param $id
	 * @return mixed
	 */
	public function getGroup($id);

	/**
	 * Gebe die Strategy für die Route zurück
	 *
	 * @param $id
	 * @return mixed
	 */
	public function getRoute($id);
}