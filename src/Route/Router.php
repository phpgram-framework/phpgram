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
	/** @var bool */
	private $slash_trim;

	/** @var CollectorInterface */
	private $collector;

	/** @var DispatcherInterface */
	private $dispatcher;

	/** @var bool */
	protected $caching;

	/** @var null|string */
	protected $cache;

	/**
	 * Router constructor.
	 * @param array $options
	 * @param MiddlewareCollectorInterface|null $middlewareCollector
	 * @param StrategyCollectorInterface|null $strategyCollector
	 */
	public function __construct(
		$options = [],
		?MiddlewareCollectorInterface $middlewareCollector = null,
		?StrategyCollectorInterface $strategyCollector = null
	){
		//setze Standard Optionen
		$options +=[
			'slash_trim'=>true,
			'caching'=>false,
			'cache'=>null,
			'dispatcher'=>'Gram\\Route\\Dispatcher\\MarkBased',
			'generator'=>'Gram\\Route\\Generator\\MarkBased',
			'parser'=>'Gram\\Route\\Parser\\StdParser',
			'collector'=>'Gram\\Route\\Collector\\RouteCollector'
		];

		$this->slash_trim = $options['slash_trim'];

		$this->caching = $options['caching'];

		$this->cache = $options['cache'];

		//Erstelle den Collector, der wird auch für andere Klassen verfügbar sein
		$this->collector= new $options['collector'](
			new $options['generator'](new $options['parser']),
			$middlewareCollector ?? new MiddlewareCollector(),
			$strategyCollector ?? new StrategyCollector()
		);

		$this->dispatcher = $options['dispatcher'];	//Dispatcher
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

		if(\is_string($this->dispatcher)) {
			//sollte der Dispatcher noch nicht erstellt sein
			$this->dispatcher = new $this->dispatcher($this->getData());
		}

		$response = $this->dispatcher->dispatch($httpMethod,$uri);

		return $this->getHandle($response);
	}

	/**
	 * Prüfe zuerst ob es einen Cache für die generierten Routes gibt
	 *
	 * Wenn ja lade den Cache
	 *
	 * Sonst hole die generierten Routes von dem RouteCollector
	 *
	 * @return array
	 */
	protected function getData()
	{
		if($this->caching && \file_exists($this->cache)){
			return require $this->cache;
		}

		$data = $this->collector->getData();

		if($this->caching) {
			\file_put_contents(
				$this->cache,
				'<?php return ' . \var_export($data, true) . ';'
			);
		}

		return $data;
	}

	/**
	 * Gibt den Handle der Route zurück
	 *
	 * bei 404 oder 405 den jeweiligen Handle
	 *
	 * @param array $response
	 * @return array
	 */
	protected function getHandle(array $response)
	{
		if($response[0] === DispatcherInterface::FOUND) {
			$route = $this->collector->getRoute($response[1]);

			if($route === null){
				$handle[self::ROUTE_HANDLER] = $this->collector->get404();
			} else{
				$handle = [
					self::ROUTE_GROUP_ID=>$route->groupid,
					self::ROUTE_ID=>$route->routeid,
					self::ROUTE_HANDLER=>$route->handle
				];
			}

			return [$response[0],$handle,$response[2]];
		}

		if($response[0] === DispatcherInterface::NOT_ALLOWED){
			 $handle[self::ROUTE_HANDLER] = $this->collector->get405();
		}else {
			$handle[self::ROUTE_HANDLER] = $this->collector->get404();
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