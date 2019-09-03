<?php
namespace Gram\Middleware\Handler;
use Gram\App\CallableCreator;
use Gram\Strategy\StrategyInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponseHandler implements RequestHandlerInterface
{
	private $stdstrategy,$callable,$param,$request,$responseFactory,$streamFactory;

	public function __construct(
		ResponseFactoryInterface $responseFactory,
		StreamFactoryInterface $streamFactory,
		StrategyInterface $strategy
	){
		$this->stdstrategy=$strategy;
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
		$strategy = $request->getAttribute('strategy',null) ?? $this->stdstrategy;

		//erstelle head
		$head=$strategy->getHeader();

		//erstelle Body
		$body=$this->streamFactory->createStream($this->createBody($strategy));

		$response=$this->responseFactory->createResponse($status);

		$response=$response
			->withBody($body)
			->withHeader($head["name"],$head["value"]);

		return $response;
	}

	protected function createBody(StrategyInterface $strategy)
	{
		$caller = new CallableCreator($this->callable);

		return $strategy->invoke($caller->getCallable(),$this->param,$this->request);
	}
}