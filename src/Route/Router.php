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

use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Collector\StrategyCollector;
use Gram\Route\Interfaces\CollectorInterface;
use Gram\Route\Interfaces\DispatcherInterface;
use Gram\Route\Interfaces\RouterInterface;
use Gram\Route\Interfaces\MiddlewareCollectorInterface;
use Gram\Route\Interfaces\StrategyCollectorInterface;

/**
 * Class Router
 * @package Gram\Route
 *
 * Der Router der von der Routingmiddleware aufgerufen wird
 *
 * Kann auch ohne Psr 7 genutzt werden
 *
 * Verschiedene Optionen für Dispatcher, Generator, Parser und Collector sind setztbar
 *
 * Führt das dispatching aus
 */
class Router implements RouterInterface
{
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const OK = 200;

	private $checkMethod,$uri,$handle,$param=[],$status,$slash_trim;

	/** @var CollectorInterface */
	private $collector;

	/** @var DispatcherInterface */
	private $dispatcher;

	/**
	 * Router constructor.
	 * @param array $options
	 * @param MiddlewareCollectorInterface|null $middlewareCollector
	 * @param StrategyCollectorInterface|null $strategyCollector
	 */
	public function __construct(
		$options=[],
		?MiddlewareCollectorInterface $middlewareCollector = null,
		?StrategyCollectorInterface $strategyCollector = null
	){

		//setze Standard Optionen
		$options +=[
			'check_method'=>true,
			'slash_trim'=>true,
			'caching'=>false,
			'cache'=>null,
			'dispatcher'=>'Gram\\Route\\Dispatcher\\DynamicDispatcher',
			'generator'=>'Gram\\Route\\Generator\\DynamicGenerator',
			'parser'=>'Gram\\Route\\Parser\\StdParser',
			'collector'=>'Gram\\Route\\Collector\\RouteCollector'
		];

		$this->slash_trim = $options['slash_trim'];
		$this->checkMethod = $options['check_method'];

		$middlewareCollector = $middlewareCollector ?? new MiddlewareCollector();
		$strategyCollector = $strategyCollector ?? new StrategyCollector();

		//Erstelle den Collector, der wird auch für andere Klassen verfügbar sein
		$this->collector= new $options['collector'](
			new $options['parser'],
			new $options['generator'],
			$middlewareCollector,
			$strategyCollector,
			$options['caching'],
			$options['cache']
		);

		$this->dispatcher= new $options['dispatcher'];	//erstelle Dispatcher
	}

	/**
	 * @inheritdoc
	 */
	public function run($uri,$httpMethod=null)
	{
		$uri=urldecode($uri);	//umlaute filtern

		if($this->slash_trim && $uri !== $this->collector->getBase().'/'){
			$uri = rtrim($uri,'/');	//entferne letzen / von der Url
		}

		$this->uri = $uri;

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

	/**
	 * Gebe dem Dispatcher die Daten vom Collector
	 *
	 * Wenn etwas gefunden setze handle und parameter und Status ok
	 *
	 * Wenn nicht setze Status 404 und gebe den 404 Handle zurück
	 *
	 * @param DispatcherInterface $dispatcher
	 * @param CollectorInterface $collector
	 * @return bool
	 */
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

	/**
	 * Prüfe die Request Method
	 *
	 * Wenn es die richtige ist gebe ok zurück
	 *
	 * Sonst 405 und den 405 Handle
	 *
	 * @param $httpMethod
	 * @param CollectorInterface $collector
	 * @return bool
	 */
	private function checkMethod($httpMethod, CollectorInterface $collector)
	{
		$httpMethod = strtolower($httpMethod);

		//Prüfe ob der Request mit der richtigen Methode durchgeführt wurde
		foreach ((array)$this->handle['method'] as $item) {
			if($httpMethod===strtolower($item)){
				return true;
			}
		}

		//Bei HEAD requests suche eine GET Route wenn fehlgeschlagen
		if($httpMethod=='head'){
			return $this->checkMethod('GET',$collector);
		}

		$this->status=self::METHOD_NOT_ALLOWED;
		$this->handle['callable']=$collector->get405();

		return false;
	}

	/**
	 * Holt von der gefunden Route den Handle vom Collector
	 *
	 * @param CollectorInterface $collector
	 */
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