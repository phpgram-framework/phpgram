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

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait ResolverTrait
 * @package Gram\Resolver
 *
 * Ein Trait, das die Getter und Setter für alle Resolver implementiert
 *
 * Variables sind public, da Closure sonst nicht darauf zugreifen kann
 */
trait ResolverTrait
{
	/** @var ServerRequestInterface */
	public $request;

	/** @var ResponseInterface */
	public $response;

	/** @var ContainerInterface|null */
	public $container;

	/**
	 * @inheritdoc
	 */
	public function setRequest(ServerRequestInterface $request):void
	{
		$this->request = $request;
	}

	/**
	 * @inheritdoc
	 */
	public function setResponse(ResponseInterface $response):void
	{
		$this->response = $response;
	}

	/**
	 * @inheritdoc
	 */
	public function getResponse():ResponseInterface
	{
		return $this->response;
	}

	/**
	 * @inheritdoc
	 */
	public function setContainer(ContainerInterface $container = null):void
	{
		$this->container = $container;
	}
}