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

namespace Gram\Route;

use Gram\Route\Interfaces\UtilCollectorInterface;

/**
 * Class RouteGroup
 * @package Gram\Route
 *
 * Ein Route Group Objekt um Middleware und Strategy für die Gruppe hinzu zufügen
 */
class RouteGroup
{
	private $groupid;

	/** @var UtilCollectorInterface */
	private $utilCollector;

	/**
	 * RouteGroup constructor.
	 * @param $groupid
	 * @param UtilCollectorInterface $utilCollector
	 */
	public function __construct($groupid, UtilCollectorInterface $utilCollector)
	{
		$this->groupid = $groupid;
		$this->utilCollector=$utilCollector;
	}

	public function addMiddleware($middleware)
	{
		$this->utilCollector->group($this->groupid,'middleware',$middleware);

		return $this;
	}

	public function addStrategy($strategy)
	{
		$this->utilCollector->groupSingle($this->groupid,'strategy',$strategy);

		return $this;
	}
}