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

use Gram\Middleware\Handler\HandlerInterface;

/**
 * Class HandlerCallback
 * @package Gram\Resolver
 *
 * Erstellt einen Handler für Middleware Klassen
 *
 * Dieser kann aufgerufen werden wenn die Middleware einen Fehler festgestellt hat und die Seite beenden will
 */
class HandlerResolver implements ResolverInterface
{
	use ResolverTrait;

	private $handler;

	public function resolve($param = [])
	{

		$return = call_user_func_array([$this->handler,'handle'],[$this->request]);

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