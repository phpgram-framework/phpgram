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

/**
 * Interface StrategyInterface
 * @package Gram\Strategy
 *
 * Ein Interface um Strategy aus zuführen
 */
interface StrategyInterface
{

	/**
	 * Führe das erhaltene Callable (von ResolverCreator)
	 *
	 * Erstelle dann je nach Strategy den Response
	 *
	 * @param ResolverInterface $resolver
	 * @param array $param
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param StreamFactoryInterface $streamFactory
	 * @return ResponseInterface
	 */
	public function invoke(
		ResolverInterface $resolver,
		array $param,
		ServerRequestInterface $request,
		ResponseInterface $response,
		StreamFactoryInterface $streamFactory
	):ResponseInterface;
}