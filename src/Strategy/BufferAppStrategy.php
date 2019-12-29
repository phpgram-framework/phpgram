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

namespace Gram\Strategy;

use Gram\Resolver\ResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
	public function invoke(
		ResolverInterface $resolver,
		array $param,
		ServerRequestInterface $request,
		ResponseInterface $response
	):ResponseInterface
	{
		$this->prepareResolver($request,$response,$resolver);

		\ob_start();
		$result = $resolver->resolve($param);

		$content = \ob_get_clean();

		//prüfe ob ein Response zurück gegeben wurde
		if($result instanceof ResponseInterface) {
			return $result;
		}

		return $this->createBody($resolver,$content);
	}
}