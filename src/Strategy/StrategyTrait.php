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

trait StrategyTrait
{
	abstract protected function getContentTypHeader():array;

	protected function prepareResolver(
		ServerRequestInterface $request,
		ResponseInterface $response,
		ResolverInterface $resolver
	) {
		[$name,$value] = $this->getContentTypHeader();

		$response = $response->withHeader($name,$value);

		$resolver->setRequest($request);
		$resolver->setResponse($response);
	}

	protected function createBody(
		ResolverInterface $resolver,
		$content
	):ResponseInterface
	{
		if($content instanceof ResponseInterface) {
			return $content;
		}

		$response = $resolver->getResponse();

		$response->getBody()->write($content);

		return $response;
	}
}