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

use Gram\Middleware\Handler\HandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class HandlerCallback
 * @package Gram\Callback
 *
 * Erstellt einen Handler für Middleware Klassen
 *
 * Dieser kann aufgerufen werden wenn die Middleware einen Fehler festgestellt hat und die Seite beenden will
 */
class HandlerCallback implements CallbackInterface
{
	private $handler;

	public function callback($param = [],ServerRequestInterface $request)
	{
		$return= call_user_func_array([$this->handler,'handle'],[$request]);

		return ($return===null)?'':$return;	//default: immer einen String zurück geben
	}

	/**
	 * @param HandlerInterface|null $handler
	 * @throws \Exception
	 */
	public function set(HandlerInterface $handler=null)
	{
		if($handler===null){
			throw new \Exception("Keinen Handler angegeben");
		}

		$this->handler=$handler;
	}
}