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
 * Interface MiddlewareCollectorInterface
 * @package Gram\Route\Interfaces
 *
 * Ein interface für alle Middleware Sammler
 */
interface MiddlewareCollectorInterface
{

	/**
	 * Fügt eine Middleware hinzu die nur bei dieser Route getriggert wird
	 *
	 * @param $routeid
	 * @param $middleware
	 * @return void
	 */
	public function addRoute($routeid,$middleware);

	/**
	 * Fügt eine Middleware hinzu die nur bei der Gruppe getriggert wird
	 *
	 * @param $groupid
	 * @param $middleware
	 * @return void
	 */
	public function addGroup($groupid,$middleware);

	/**
	 * Gibt die Middleware für die Route Group zurück
	 *
	 * oder null wenn keine gefunden wurde
	 * @see addGroup()
	 *
	 * @param $id
	 * @return array|null
	 */
	public function getGroup($id);

	/**
	 * Gibt die Middleware für die Route zurück
	 *
	 * oder null wenn keine gefunden wurde
	 *
	 * @see addRoute()
	 *
	 * @param $id
	 * @return mixed
	 */
	public function getRoute($id);
}