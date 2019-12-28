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
use Psr\Http\Message\StreamFactoryInterface;

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
		$content,
		ResponseInterface $response,
		StreamFactoryInterface $streamFactory,
		bool $writeInResponse = true
	):ResponseInterface
	{
		if(\is_string($content)){
			if($writeInResponse) {
				$response->getBody()->write($content);

				return $response;
			}

			//Wenn Return ein String ist erstelle Body aus zurück gegebem String
			$body = $streamFactory->createStream($content);
		}else {
			//Sonst erstelle Body aus einer Resource
			$body = $streamFactory->createStreamFromResource($content);
		}

		return $response->withBody($body);
	}
}