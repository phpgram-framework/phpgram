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


abstract class Dispatcher implements DispatcherInterface
{
	/**
	 * @inheritdoc
	 */
	public function dispatch($method,$uri, array $routes=[])
	{
		if(isset($routes['static'][$uri])){
			$handler = $routes['static'][$uri];

			$status = $this->checkMethod($method,$handler['method']);

			return [$status,$handler,[]];
		}

		//wenn es keine Dnymic Routes gibt
		if(!isset($routes['dynamic'])){
			return [self::NOT_FOUND];
		}

		$response = $this->dispatchDynamic(
			$uri,
			$routes['dynamic']['regexes'],
			$routes['dynamic']['dynamichandler']
		);

		if($response[0] === self::FOUND){
			$handler = $response[1];

			$status = $this->checkMethod($method,$handler['method']);

			return [$status,$handler,$response[2]];
		}

		return [self::NOT_FOUND];
	}

	/**
	 * Prüfe die Http Method
	 *
	 * Wenn diese Head ist prüfe ob es auch eine Get Method gibt
	 *
	 * @param $method
	 * Die zuprüfende Method
	 *
	 * @param array $route_method
	 * Die Methods der Route
	 *
	 * @return int
	 */
	protected function checkMethod($method,array $route_method)
	{
		$httpMethod = \strtolower($method);

		//Prüfe ob der Request mit der richtigen Methode durchgeführt wurde
		foreach ((array)$route_method as $item) {
			if($httpMethod === \strtolower($item)){
				return self::FOUND;
			}
		}

		//Bei HEAD requests suche eine GET Route wenn fehlgeschlagen
		if($httpMethod=='head'){
			return $this->checkMethod('GET',$route_method);
		}

		return self::NOT_ALLOWED;
	}
}