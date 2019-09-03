<?php
namespace Gram\Route\Collector;
use Gram\Route\Interfaces\StrategyCollectorInterface;

class StrategyCollector implements StrategyCollectorInterface
{
	private $std,$group=[],$route=[];

	public function addStd($strategy)
	{
		$this->std=$strategy;
	}

	public function addRoute($routeid, $strategy)
	{
		$this->route[$routeid]=$strategy;
	}

	public function addGroup($groupid, $strategy)
	{
		$this->group[$groupid]=$strategy;
	}

	public function getStd()
	{
		return $this->std;
	}

	public function getGroup($id)
	{
		if(isset($this->group[$id])){
			return $this->group[$id];
		}
	}

	public function getRoute($id)
	{
		if(isset($this->route[$id])){
			return $this->route[$id];
		}
	}
}