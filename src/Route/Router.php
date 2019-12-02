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
	private $slash_trim;

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
			'slash_trim'=>true,
			'caching'=>false,
			'cache'=>null,
			'dispatcher'=>'Gram\\Route\\Dispatcher\\DynamicDispatcher',
			'generator'=>'Gram\\Route\\Generator\\DynamicGenerator',
			'parser'=>'Gram\\Route\\Parser\\StdParser',
			'collector'=>'Gram\\Route\\Collector\\RouteCollector'
		];

		$this->slash_trim = $options['slash_trim'];

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
	public function run($uri,$httpMethod)
	{
		$uri = \urldecode($uri);	//umlaute filtern

		if($this->slash_trim && $uri !== $this->collector->getBase().'/'){
			$uri = \rtrim($uri,'/');	//entferne letzen / von der Url
		}

		[$status,$handle,$param] = $this->dispatch($httpMethod,$uri);

		if($status!=DispatcherInterface::FOUND){
			return [$status,$handle,$param];
		}

		$routeid = $handle['routeid'];

		$handle['callable'] = $this->collector->getHandle()[$routeid];

		return [$status,$handle,$param];
	}

	/**
	 * Gebe dem Dispatcher die Daten vom Collector
	 *
	 * Wenn etwas gefunden setze handle und parameter und Status ok
	 *
	 * Wenn nicht setze Status 404 und gebe den 404 Handle zurück
	 *
	 * @param $method
	 * @param $uri
	 * @return array[int,array,array]
	 */
	protected function dispatch($method,$uri)
	{
		$response = $this->dispatcher->dispatch($method,$uri,$this->collector->getData());

		if($response[0]===DispatcherInterface::FOUND){
			return $response;
		}

		if($response[0]===DispatcherInterface::NOT_ALLOWED){
			$handle['callable']=$this->collector->get405();
		}else{
			$handle['callable']=$this->collector->get404();
		}

		return [$response[0],$handle,[]];
	}

	/**
	 * @inheritdoc
	 */
	public function getCollector()
	{
		return $this->collector;
	}
}