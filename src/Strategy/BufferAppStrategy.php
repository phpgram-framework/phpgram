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
 * Class BufferAppStrategy
 * @package Gram\Strategy
 *
 * Strategy die den Output des Callable in dem Outputbuffer sammelt und dann zurück gibt
 *
 * Callable wird genau so ausgeführt wie die @see StdAppStrategy
 */
class BufferAppStrategy extends StdAppStrategy
{

	/**
	 * @inheritdoc
	 */
	public function invoke(ResolverInterface $resolver, array $param)
	{
		ob_start();
		parent::invoke($resolver,$param);
		$return=ob_get_clean();
		ob_flush();

		return $return;
	}
}