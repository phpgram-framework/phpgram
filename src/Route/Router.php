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

	private $checkMethod, $slash_trim;

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
		$uri = \urldecode($uri);	//umlaute filtern

		if($this->slash_trim && $uri !== $this->collector->getBase().'/'){
			$uri = \rtrim($uri,'/');	//entferne letzen / von der Url
		}

		[$status,$handle,$param] = $this->dispatch($uri);

		if($status==self::NOT_FOUND){
			return [$status,$handle,$param];
		}

		if(isset($httpMethod) && $this->checkMethod && !$this->checkMethod($handle['method'],$httpMethod)){
			$handle['callable'] = $this->collector->get405();

			return [self::METHOD_NOT_ALLOWED,$handle,$param];
		}

		$routeid = $handle['routeid'];

		$handle['callable'] = $this->collector->getHandle()[$routeid];

		return [self::OK,$handle,$param];
	}

	/**
	 * Gebe dem Dispatcher die Daten vom Collector
	 *
	 * Wenn etwas gefunden setze handle und parameter und Status ok
	 *
	 * Wenn nicht setze Status 404 und gebe den 404 Handle zurück
	 *
	 * @param $uri
	 * @return array[int,array,array]
	 */
	protected function dispatch($uri)
	{
		$response = $this->dispatcher->dispatch($uri,$this->collector->getData());

		if($response[0]===DispatcherInterface::FOUND){
			return [self::OK,$response[1],$response[2]];
		}

		$handle['callable']=$this->collector->get404();

		return [self::NOT_FOUND,$handle,[]];
	}

	/**
	 * Prüfe die Request Method
	 *
	 * Wenn es die richtige ist gebe ok zurück
	 *
	 * Sonst 405 und den 405 Handle
	 *
	 * @param $route_method
	 * @param $httpMethod
	 * @return bool
	 */
	protected function checkMethod($route_method,$httpMethod)
	{
		$httpMethod = \strtolower($httpMethod);

		//Prüfe ob der Request mit der richtigen Methode durchgeführt wurde
		foreach ((array)$route_method as $item) {
			if($httpMethod === \strtolower($item)){
				return true;
			}
		}

		//Bei HEAD requests suche eine GET Route wenn fehlgeschlagen
		if($httpMethod=='head'){
			return $this->checkMethod($route_method,'GET');
		}

		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function getCollector()
	{
		return $this->collector;
	}
}