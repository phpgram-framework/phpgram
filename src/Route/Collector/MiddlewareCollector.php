<?php
namespace Gram\Route\Collector;
use Gram\Route\Interfaces\MiddlewareCollectorInterface;

class MiddlewareCollector implements MiddlewareCollectorInterface
{
	private $std=[],$route=[],$group=[];

	public function addStd($middleware, $order = null){
		$this->std[]=$middleware;
		return $this;
	}

	public function addRoute($routeid, $middleware, $order = null){
		$this->route[$routeid][]=$middleware;
	}

	public function addGroup($groupid, $middleware, $order = null){
		$this->group[$groupid][]=$middleware;
	}

	public function getStdMiddleware(){
		return $this->std;
	}

	public function getGroup($id){
		if(isset($this->group[$id]))
			return $this->group[$id];
	}

	public function getRoute($id){
		if(isset($this->route[$id]))
			return $this->route[$id];
	}
}