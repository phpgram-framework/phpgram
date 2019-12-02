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

namespace Gram\Route\Dispatcher\MethodSort;

use Gram\Route\Interfaces\DispatcherInterface;

abstract class Dispatcher implements DispatcherInterface
{
	/**
	 * @inheritdoc
	 */
	public function dispatch($method,$uri, array $routes=[])
	{
		if(isset($routes['static'][$method][$uri])){
			return [self::FOUND,$routes['static'][$method][$uri],[]];
		}

		//wenn es keine Dnymic Routes gibt
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