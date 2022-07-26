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
 * Class CallableResolver
 * @package Gram\Resolver
 *
 * Führt ein Callable aus
 *
 * Wenn Response verändert werden soll muss dieser zurück gegeben werden
 */
class CallableResolver implements ResolverInterface
{
	use ResolverTrait;

	/** @var callable */
	protected $callable;

	/**
	 * @inheritdoc
	 */
	public function resolve(array $param)
	{
		$return = \call_user_func_array($this->callable,[$this->request,$this->response,$param]);

		return $return ?? '';	//default: immer einen String zurück geben
	}

	/**
	 * @inheritdoc
	 *
	 * @throws CallableNotAllowedException
	 */
	public function set(callable $callable = null): void
	{
		if(!\is_callable($callable)) {
			throw new CallableNotAllowedException("No callable given");
		}

		$this->callable = $callable;
	}
}