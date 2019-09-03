<?php
namespace Gram\Route;
use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Collector\StrategyCollector;
use Gram\Route\Interfaces\CollectorInterface;
use Gram\Route\Interfaces\DispatcherInterface;
use Gram\Route\Interfaces\RouterInterface;
use Gram\Route\Interfaces\MiddlewareCollectorInterface;
use Gram\Route\Interfaces\StrategyCollectorInterface;

class Router implements RouterInterface
{
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const OK = 200;

	private $checkMethod,$uri,$handle,$param=[],$status;
	private $collector,$dispatcher;

	public function __construct(
		$checkMethod=true,
		$options=[],
		?MiddlewareCollectorInterface $middlewareCollector = null,
		?StrategyCollectorInterface $strategyCollector = null
	){
		$this->checkMethod=$checkMethod;

		$options +=[
			'caching'=>false,
			'cache'=>null,
			'dispatcher'=>'Gram\\Route\Dispatcher\\DynamicDispatcher',
			'generator'=>'Gram\\Route\\Generator\\DynamicGenerator',
			'parser'=>'Gram\\Route\\Parser\\StdParser',
			'collector'=>'Gram\\Route\\Collector\\RouteCollector'
		];

		$middlewareCollector = $middlewareCollector ?? new MiddlewareCollector();
		$strategyCollector = $strategyCollector ?? new StrategyCollector();

		$this->collector= new $options['collector'](
			new $options['parser'],
			new $options['generator'],
			$middlewareCollector,
			$strategyCollector,
			$options['caching'],
			$options['cache']
		);

		$this->dispatcher= new $options['dispatcher'];
	}

	public function run($uri,$httpMethod=null)
	{
		$this->uri=urldecode($uri);	//umlaute filtern

		if(!$this->dispatch($this->dispatcher,$this->collector)){
			return false;
		}

		if(isset($httpMethod) && $this->checkMethod && !$this->checkMethod($httpMethod,$this->collector)){
			return false;
		}

		$this->buildHandle($this->collector);
		$this->status=self::OK;

		return true;
	}


	private function dispatch(DispatcherInterface $dispatcher,CollectorInterface $collector)
	{
		$dispatcher->setData($collector->getData());

		$response = $dispatcher->dispatch($this->uri);

		if($response[0]===DispatcherInterface::FOUND){
			$this->handle=$response[1];
			$this->param=$response[2];
			return true;
		}

		$this->status=self::NOT_FOUND;
		$this->handle['callable']=$collector->get404();

		return false;
	}

	private function checkMethod($httpMethod, CollectorInterface $collector)
	{
		//Prüfe ob der Request mit der richtigen Methode durchgeführt wurde
		foreach ((array)$this->handle['method'] as $item) {
			if(strtolower($httpMethod)===strtolower($item)){
				return true;
			}
		}

		$this->status=self::METHOD_NOT_ALLOWED;
		$this->handle['callable']=$collector->get405();

		return false;
	}

	private function buildHandle(CollectorInterface $collector)
	{
		$routeid=$this->handle['routeid'];

		$this->handle['callable'] = $collector->getHandle()[$routeid];
	}

	/**
	 * @return mixed
	 */
	public function getHandle()
	{
		return $this->handle;
	}

	/**
	 * @return mixed
	 */
	public function getParam()
	{
		return $this->param;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return CollectorInterface
	 */
	public function getCollector()
	{
		return $this->collector;
	}
}