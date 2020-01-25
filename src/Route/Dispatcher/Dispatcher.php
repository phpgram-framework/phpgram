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
	 * @inheritdoc
	 *
	 * Sucht auch noch die Http Method für 405
	 */
	public function dispatch($method,$uri, array $routes=[])
	{
		$response = $this->doDispatch($method,$uri,$routes);

		if($response[0]===self::FOUND){
			return $response;
		}

		//wenn keine Route gefunden bei HEAD versuch GET
		if($method=='HEAD'){
			return $this->dispatch('GET',$uri,$routes);
		}

		//alle Http Methods aus Static und Dynamic
		$methods = \array_unique(\array_merge(\array_keys($routes['static'] ?? []),\array_keys($routes['dynamic']['regexes'] ?? [])));

		//durchlaufe alle Http Methods
		foreach ($methods as $item) {
			//wenn Method nicht die Anfangsmethod: suche die Route da, wenn gefunden gebe 405 aus
			if($item!=$method){
				$response = $this->doDispatch($item,$uri,$routes);

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
	 * @param array $routes
	 * @return array
	 */
	protected function doDispatch($method,$uri, array $routes=[])
	{
		if(isset($routes['static'][$method][$uri])){
			return [self::FOUND,$routes['static'][$method][$uri],[]];
		}

		//wenn es keine Dynamic Routes gibt
		if(!isset($routes['dynamic']['regexes'][$method])){
			return [self::NOT_FOUND];
		}

		return $this->dispatchDynamic(
			$uri,
			$routes['dynamic']['regexes'][$method],
			$routes['dynamic']['dynamichandler'][$method]
		);
	}
}