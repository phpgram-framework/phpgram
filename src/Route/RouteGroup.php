<?php
namespace Gram\Route;
use Gram\Route\Interfaces\MiddlewareCollectorInterface;
use Gram\Route\Interfaces\StrategyCollectorInterface;

class RouteGroup
{
	private $groupid,$stack,$strategyCollector;

	public function __construct(
		$prefix,
		$groupid,
		MiddlewareCollectorInterface $stack,
		StrategyCollectorInterface $strategyCollector
	){
		$this->groupid=$groupid;
		$this->stack=$stack;
		$this->strategyCollector=$strategyCollector;
	}

	public function addMiddleware($middleware,$order=null)
	{
		$this->stack->addGroup($this->groupid,$middleware,$order);

		return $this;
	}

	public function addStrategy($strategy)
	{
		$this->strategyCollector->addGroup($this->groupid,$strategy);

		return $this;
	}
}