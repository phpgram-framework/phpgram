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

use Gram\Route\Interfaces\MiddlewareCollectorInterface;
use Gram\Route\Interfaces\StrategyCollectorInterface;

/**
 * Class Route
 * @package Gram\Route
 *
 * Ein Routeobjekt, dass Informationen über die Route enthält
 *
 * wird vom Routecollecotr aufgerufen wenn eine neue Route hinzugefügt wird
 *
 * Bietet die Möglichkeit Middleware und Strategy für die Route hinzu zufügen
 */
class Route
{
	public $path,$handle,$vars = [],$groupid=[],$routeid,$method;

	/** @var MiddlewareCollectorInterface */
	private $stack;

	/** @var StrategyCollectorInterface */
	private $strategyCollector;

	/**
	 * Route constructor.
	 * @param string $path
	 * @param $handle
	 * @param $method
	 * @param $routegroupid
	 * @param $routeid
	 * @param MiddlewareCollectorInterface $stack
	 * @param StrategyCollectorInterface $strategyCollector
	 */
	public function __construct(
		string $path,
		$handle,
		$method,
		$routegroupid,
		$routeid,
		MiddlewareCollectorInterface $stack,
		StrategyCollectorInterface $strategyCollector
	){
		$this->method=$method;	//speichere Method für Dispatcher
		$this->path=$path;
		$this->handle = $handle;
		$this->groupid=$routegroupid;
		$this->routeid=$routeid;
		$this->stack=$stack;
		$this->strategyCollector=$strategyCollector;
	}

	/**
	 * Kann nach dem definieren einer Route aufgerufen werden um mehre Middleware hinzu zufügen
	 *
	 * @param $middleware
	 * @return $this
	 */
	public function addMiddleware($middleware)
	{
		$this->stack->addRoute($this->routeid,$middleware);

		return $this;
	}

	public function addStrategy($strategy)
	{
		$this->strategyCollector->addRoute($this->routeid,$strategy);

		return $this;
	}
}