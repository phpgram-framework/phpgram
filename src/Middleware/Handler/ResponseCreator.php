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

namespace Gram\Middleware\Handler;

use Gram\Exceptions\CallableNotFoundException;
use Gram\Strategy\StrategyInterface;
use Gram\ResolverCreator\ResolverCreatorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ResponseHandler
 * @package Gram\Middleware\Handler
 *
 * Ein Standard Psr 15 Handler der Requests zu Response umbaut
 *
 * Wird als letztes im QueueHandler aufgerufen
 *
 * Kann auch von anderen Klassen aufgerufen werden um einen Response zu erstellen
 */
class ResponseCreator implements RequestHandlerInterface
{
	private $stdstrategy, $creator, $container, $responseFactory, $streamFactory;

	public function __construct(
		ResponseFactoryInterface $responseFactory,
		StreamFactoryInterface $streamFactory,
		ResolverCreatorInterface $creator,
		StrategyInterface $strategy,
		ContainerInterface $container=null
	){
		$this->stdstrategy=$strategy;
		$this->creator=$creator;
		$this->responseFactory=$responseFactory;
		$this->streamFactory=$streamFactory;
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
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 * @throws CallableNotFoundException
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		//Die Attribute die für das Ausführen des callable benötigt werden
		$callable = $request->getAttribute('callable');

		if($callable===null){
			throw new CallableNotFoundException("No callable to Resolve");
		}

		$param = $request->getAttribute('param',[]);
		$strategy = $request->getAttribute('strategy',null) ?? $this->stdstrategy;
		$status = $request->getAttribute('status',200);

		//erstelle Response mit den Werten von den Middleware
		$response = $this->responseFactory->createResponse($status);

		//erstelle header der Strategy
		$content_typ_head = $strategy->getHeader();

		$response = $response->withHeader($content_typ_head["name"],$content_typ_head["value"]);

		//Führe Callable aus
		return $this->createBody($callable,$param,$strategy,$request,$response);
	}

	/**
	 * Erstelle den Content für den Body
	 *
	 * Erstelle aus dem callable ein CallbackInterface
	 *
	 * führe das CallbackInterface mit der gesetzen Strategy aus
	 *
	 * Nehme des return des Callbacks entgegen
	 *
	 * Nehme den Response des Callbacks entgegen, da diese ggf. verändert wurden
	 * durch das Callback
	 *
	 * @param $callable
	 * @param array $param
	 * @param StrategyInterface $strategy
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	protected function createBody(
		$callable,
		array $param=[],
		StrategyInterface $strategy,
		ServerRequestInterface $request,
		ResponseInterface $response
	) {
		$resolver = $this->creator->createResolver($callable);

		$resolver->setRequest($request);
		$resolver->setResponse($response);
		$resolver->setContainer($this->container);

		$content = $strategy->invoke($resolver,$param);

		if($content instanceof ResponseInterface){
			//Wenn der Return bereits ein Response ist
			return $content;
		}

		$response = $resolver->getResponse();

		if(\is_string($content)){
			//Wenn Return ein String ist erstelle Body aus zurück gegebem String
			$body = $this->streamFactory->createStream($content);
		}else {
			//Sonst erstelle Body aus einer Resource
			$body = $this->streamFactory->createStreamFromResource($content);
		}

		return $response->withBody($body);
	}
}