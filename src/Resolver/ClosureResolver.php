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

namespace Gram\Resolver;

use Gram\Exceptions\CallableNotAllowedException;

/**
 * Class CallbackHandler
 * @package Gram\Resolver
 *
 * Führt eine Closure aus
 */
class ClosureResolver implements ResolverInterface
{
	use ResolverTrait;

	/** @var \Closure */
	protected $callback;

	/**
	 * Führe das Callback aus
	 *
	 * Binde es vorher an dieses Object
	 *
	 * @param array $param
	 * @return mixed|string
	 */
	public function resolve(array $param)
	{
		$callback = $this->callback->bindTo($this);	//Bindet die Funktion an diese Klasse, somit hat sie Zugriff auf den Request

		$return = \call_user_func_array($callback,$param);

		return $return ?? '';	//default: immer einen String zurück geben
	}

	/**
	 * @inheritdoc
	 *
	 * @throws \Exception
	 */
	public function set(\Closure $callback=null):void
	{
		if(! $callback instanceof \Closure){
			throw new CallableNotAllowedException("No Closure given");
		}

		$this->callback=$callback;
	}

	/**
	 * Biete die Möglichkeit auf Values im Container zu zugreifen mit
	 * $this->value
	 *
	 * Sollte der Value nicht im Container enthalten sein wird eine Exception geworfen
	 *
	 * @param $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->container->get($name);
	}
}