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
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\Route\Dispatcher;

use Gram\Route\Interfaces\DispatcherInterface;

/**
 * Class Dispatcher
 * @package Gram\Route\Dispatcher
 *
 * Der Hauptdispatcher durchsucht nur die static Routes
 */
abstract class Dispatcher implements DispatcherInterface
{
	private $routes;

	public function setData(array $routes)
	{
		$this->routes=$routes;
	}

	/**
	 * @inheritdoc
	 */
	public function dispatch($uri)
	{
		if(isset($this->routes['static'][$uri])){
			return [self::FOUND,$this->routes['static'][$uri],[]];
		}

		//wenn es keine Dnymic Routes gibt
		if(!isset($this->routes['dynamic'])){
			return [self::NOT_FOUND];
		}

		return $this->dispatchDynamic(
			$uri,
			$this->routes['dynamic']['regexes'],
			$this->routes['dynamic']['dynamichandler']
		);
	}
}