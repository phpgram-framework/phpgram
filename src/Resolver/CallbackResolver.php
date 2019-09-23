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

namespace Gram\Resolver;

/**
 * Class CallbackHandler
 * @package Gram\Resolver
 *
 * Speichert ein Callable und gibt es wieder zurück
 */
class CallbackResolver implements ResolverInterface
{
	use ResolverTrait;

	/** @var \Closure */
	protected $callback;

	/**
	 * Führe das Callback aus
	 *
	 * @param array $param
	 * @return mixed|string
	 */
	public function resolve($param=[])
	{
		$callback = $this->callback->bindTo($this);	//Bindet die Funktion an diese Klasse, somit hat sie Zugriff auf den Request

		$return = call_user_func_array($callback,$param);

		return ($return===null)?'':$return;	//default: immer einen String zurück geben
	}

	/**
	 * @param null $callback
	 * @throws \Exception
	 */
	public function set($callback=null)
	{
		if(!is_callable($callback)){
			throw new \Exception("Kein Callable!");
		}

		$this->callback=$callback;
	}
}