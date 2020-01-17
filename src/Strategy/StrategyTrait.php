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
 * Trait StrategyTrait
 * @package Gram\Strategy
 *
 * Trait für Strategies um einen Response zurück zugeben
 * der einen String Content enthält
 */
trait StrategyTrait
{
	/**
	 * Gebe den Content-Type header der jeweiligen Strategy zurück
	 *
	 * @return array
	 */
	abstract protected function getContentTypHeader():array;

	/**
	 * Übergibt request und response dem Resolver
	 * Container wurde bereits im ResponseCreator übergeben
	 *
	 * Erstelle zudem auch den Content-Type Header
	 *
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param ResolverInterface $resolver
	 */
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

	/**
	 * Erstelle den Body des Response
	 *
	 * Soltle der return des Resolvers bereits ein Response sein gebe diesen zurück
	 *
	 * Sonst hole den Response aus dem Resolver (falls dieser geändert wurde)
	 * und schreibe den Return des Resolvers in den Response
	 *
	 * @param ResolverInterface $resolver
	 * @param $content
	 * @return ResponseInterface
	 */
	protected function createBody(ResolverInterface $resolver, &$content):ResponseInterface
	{
		if($content instanceof ResponseInterface) {
			return $content;
		}

		$response = $resolver->getResponse();

		$response->getBody()->write($content);

		return $response;
	}
}