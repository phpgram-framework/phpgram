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
 * Class StdAppStrategy
 * @package Gram\Strategy
 *
 * Strategy die das Callable ausführt und den Return des Callable zurück gibt
 */
class StdAppStrategy implements StrategyInterface
{
	use StrategyTrait;

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

		$content = $resolver->resolve($param);

		return $this->createBody($resolver,$content);
	}

	protected function getContentTypHeader(): array
	{
		return ['Content-Type','text/html'];
	}
}