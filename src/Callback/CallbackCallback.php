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

namespace Gram\Callback;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CallbackHandler
 * @package Gram\Handler
 *
 * Speichert ein Callable und gibt es wieder zurück
 */
class CallbackCallback implements CallbackInterface
{
	/** @var \Closure */
	protected $callback;

	/** @var ServerRequestInterface */
	public $request;

	/**
	 * Führe das Callback aus
	 *
	 * @param array $param
	 * @param ServerRequestInterface $request
	 * @return mixed|string
	 */
	public function callback($param=[],ServerRequestInterface $request)
	{
		$this->request = $request;

		$callback = $this->callback->bindTo($this);	//Bindet die Funktion an diese Klasse, somit hat sie Zugriff auf den Request

		$return = call_user_func_array($callback,$param);

		return ($return===null)?'':$return;	//default: immer einen String zurück geben
	}

	public function getRequest(): ServerRequestInterface
	{
		return $this->request;
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