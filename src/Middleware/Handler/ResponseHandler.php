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

namespace Gram\Middleware\Handler;

use Gram\Strategy\StrategyInterface;
use Gram\CallbackCreator\CallbackCreatorInterface;
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
class ResponseHandler implements RequestHandlerInterface
{
	private $stdstrategy,$creator,$callable,$param,$request,$responseFactory,$streamFactory;

	public function __construct(
		ResponseFactoryInterface $responseFactory,
		StreamFactoryInterface $streamFactory,
		CallbackCreatorInterface $creator,
		StrategyInterface $strategy
	){
		$this->stdstrategy=$strategy;
		$this->creator=$creator;
		$this->responseFactory=$responseFactory;
		$this->streamFactory=$streamFactory;
	}

	//handler der das callback zusammenbaut. wird erst zuletzt ausgeführt

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$this->request=$request;
		$this->callable= $request->getAttribute('callable');
		$this->param=$request->getAttribute('param',[]);
		$status=$request->getAttribute('status',200);
		$reason = $request->getAttribute('reason','');
		$header = $request->getAttribute('header',[]);
		$strategy = $request->getAttribute('strategy',null) ?? $this->stdstrategy;
		$creator = $request->getAttribute('creator',null) ?? $this->creator;

		//erstelle head
		$head=$strategy->getHeader();

		//erstelle Body
		$body=$this->streamFactory->createStream($this->createBody($strategy,$creator));

		$response=$this->responseFactory->createResponse($status,$reason);

		$response=$response
			->withBody($body)
			->withHeader($head["name"],$head["value"]);

		//Fügt Custom Header hinzu
		foreach ($header as $item) {
			$response=$response->withHeader($item["name"],$item['value']);
		}

		return $response;
	}

	protected function createBody(StrategyInterface $strategy,CallbackCreatorInterface $creator)
	{
		$creator->createCallback($this->callable);

		return $strategy->invoke($creator->getCallable(),$this->param,$this->request);
	}
}