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

namespace Gram\Middleware;

use Gram\Exceptions\CallableNotFoundException;
use Gram\Exceptions\StrategyNotAllowedException;
use Gram\Strategy\StrategyInterface;
use Gram\ResolverCreator\ResolverCreatorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ResponseHandler
 * @package Gram\Middleware
 *
 * Ein Standard Psr 15 Handler der Requests zu Response umbaut
 *
 * Wird als letztes im QueueHandler aufgerufen
 *
 * Kann auch von anderen Klassen aufgerufen werden um einen Response zu erstellen
 */
final class ResponseCreator implements RequestHandlerInterface
{
	/** @var StrategyInterface  */
	private $stdstrategy;

	/** @var ResolverCreatorInterface */
	private $creator;

	/** @var ContainerInterface */
	private $container;

	/** @var ResponseFactoryInterface */
	private $responseFactory;

	public function __construct(
		ResponseFactoryInterface $responseFactory,
		ResolverCreatorInterface $creator,
		StrategyInterface $strategy,
		ContainerInterface $container=null
	){
		$this->stdstrategy=$strategy;
		$this->creator=$creator;
		$this->responseFactory=$responseFactory;
		$this->container = $container;
	}

	/**
	 * @inheritdoc
	 *
	 * Baut das Callback zusammen
	 *
	 * Erstellt den Response anhand des Requests
	 *
	 * Wird zuletzt im @see \Gram\App\QueueHandler ausgeführt
	 *
	 * @throws CallableNotFoundException
	 * @throws StrategyNotAllowedException
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		//Die Attribute die für das Ausführen des callable benötigt werden
		$callable = $request->getAttribute(RouteMiddleware::CALLABLE);

		if($callable === null){
			throw new CallableNotFoundException("No callable to Resolve");
		}

		$param = $request->getAttribute(RouteMiddleware::ROUTE_PARAMETER,[]);
		$strategy = $request->getAttribute(RouteMiddleware::ROUTE_STRATEGY,null);

		if ($strategy !== null) {
			//Für diese Route wurde eine andere Strategy definiert
			if ($this->container !== null && \is_string($strategy)) {
				//Suche im Controller wenn der Key angegeben wurde
				$strategy = $this->container->get($strategy);
			}

			if($strategy instanceof StrategyInterface === false) {
				throw new StrategyNotAllowedException("Strategy needs to implement StrategyInterface");
			}
		} else {
			//sonst benutze die Standard Strategy die immer verwendet werden soll (@see App)
			$strategy = $this->stdstrategy;
		}

		$status = $request->getAttribute(RouteMiddleware::STATUS,200);

		//erstelle Response mit den Werten von den Middleware
		$response = $this->responseFactory->createResponse($status);

		$resolver = $this->creator->createResolver($callable);
		$resolver->setContainer($this->container);

		//Führe Callable aus
		return $strategy->invoke($resolver,$param,$request,$response);
	}
}