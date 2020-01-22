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
 * Eine Möglichkeit ein Object aus zuführen
 *
 * Vorteil gegenüber callable Objects:
 * Kann Response verändern ohne diesen zurück gegeben zumüssen
 */
class HandlerResolver implements ResolverInterface
{
	use ResolverTrait;

	/** @var HandlerInterface */
	private $handler;

	/**
	 * @inheritdoc
	 */
	public function resolve(array $param)
	{
		$this->handler->setPsr($this->request,$this->response);
		$this->handler->setContainer($this->container);

		$return = \call_user_func([$this->handler,'handle']);

		$this->response = $this->handler->getResponse();

		return $return ?? '';	//default: immer einen String zurück geben
	}

	/**
	 * @inheritdoc
	 *
	 * @throws \Exception
	 */
	public function set(HandlerInterface $handler=null):void
	{
		if($handler===null){
			throw new ClassNotAllowedException("No Handler set");
		}

		$this->handler=$handler;
	}
}