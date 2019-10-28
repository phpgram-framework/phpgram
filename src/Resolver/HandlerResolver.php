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

use Gram\Exceptions\ClassNotAllowedException;
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

	/** @var HandlerInterface */
	private $handler;

	public function resolve($param = [])
	{
		$this->handler->setPsr($this->request,$this->response);
		$this->handler->setContainer($this->container);

		$return = \call_user_func([$this->handler,'handle']);

		$this->response = $this->handler->getResponse();

		return ($return===null)?'':$return;	//default: immer einen String zurück geben
	}

	/**
	 * @param HandlerInterface|null $handler
	 * @throws \Exception
	 */
	public function set(HandlerInterface $handler=null)
	{
		if($handler===null){
			throw new ClassNotAllowedException("No Handler set");
		}

		$this->handler=$handler;
	}
}