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

namespace Gram\Strategy;

use Gram\Resolver\ResolverInterface;

/**
 * Class StdAppStrategy
 * @package Gram\Strategy
 *
 * Strategy die das Callable ausführt und den Return des Callable zurück gibt
 */
class StdAppStrategy implements StrategyInterface
{
	/**
	 * @inheritdoc
	 */
	public function getHeader()
	{
		return ["name"=>'Content-Type',"value"=>'text/html'];
	}

	/**
	 * @inheritdoc
	 */
	public function invoke(ResolverInterface $resolver, array $param)
	{
		return $resolver->resolve($param);
	}
}