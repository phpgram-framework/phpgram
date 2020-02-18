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

namespace Gram\Route\Dispatcher;

use Gram\Route\Interfaces\DispatcherInterface;

/**
 * Class Dispatcher
 * @package Gram\Route\Dispatcher\MethodSort
 *
 * Ein Dispatcher der die Routes, mit ihrer Http Method sortiert, dispatch
 */
abstract class Dispatcher implements DispatcherInterface
{
	/**
	 * Die Static Routes und ihre Handler
	 * (ohne Parameter)
	 *
	 * @var array
	 */
	protected $staticRoutes;

	/**
	 * Regex der dynamischen Routes
	 * (mit Parametern)
	 *
	 * @var array
	 */
	protected $dynamicRoutesRegex;

	/**
	 * Handler der dynamischen Routes
	 *
	 * @var array
	 */
	protected $dynamicRoutesHandler;

	/**
	 * Dispatcher constructor.
	 *
	 * @param array $routes		Array mit allen geparsten Routes
	 */
	public function __construct(array $routes)
	{
		$this->staticRoutes = $routes['static'] ?? [];
		$this->dynamicRoutesRegex = $routes['dynamic']['regexes'] ?? [];
		$this->dynamicRoutesHandler = $routes['dynamic']['dynamichandler'] ?? [];
	}

	/**
	 * @inheritdoc
	 *
	 * Sucht auch noch die Http Method für 405
	 */
	public function dispatch($method,$uri)
	{
		$response = $this->doDispatch($method,$uri);

		if($response[0]===self::FOUND){
			return $response;
		}

		//wenn keine Route gefunden bei HEAD versuch GET
		if($method=='HEAD'){
			return $this->dispatch('GET',$uri);
		}

		//alle Http Methods aus Static und Dynamic
		$methods = \array_unique(\array_merge(\array_keys($this->staticRoutes),\array_keys($this->dynamicRoutesRegex)));

		//durchlaufe alle Http Methods
		foreach ($methods as $item) {
			//wenn Method nicht die Anfangsmethod: suche die Route da, wenn gefunden gebe 405 aus
			if($item!=$method){
				$response = $this->doDispatch($item,$uri);

				if($response[0]===self::FOUND){
					return [self::NOT_ALLOWED];
				}
			}
		}

		return [self::NOT_FOUND];
	}

	/**
	 * Triggert jeweils static und Dynamic Dispatcher mit der jeweiligen Method
	 *
	 * @param $method
	 * @param $uri
	 * @return array
	 */
	protected function doDispatch($method,$uri)
	{
		if(isset($this->staticRoutes[$method][$uri])){
			return [self::FOUND,$this->staticRoutes[$method][$uri],[]];
		}

		//wenn es keine Dynamic Routes gibt
		if(!isset($this->dynamicRoutesRegex[$method])){
			return [self::NOT_FOUND];
		}

		return $this->dispatchDynamic($method,$uri);
	}
}