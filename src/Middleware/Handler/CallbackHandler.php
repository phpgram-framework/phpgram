<?php
namespace Gram\Middleware\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Gram\App\CallableCreator;

class CallbackHandler extends Handler
{
	//handler der das callback zusammenbaut. wird erst zuletzt ausgeführt

	public function handle(ServerRequestInterface $request): ResponseInterface{
		$handle= $request->getAttribute('handle');
		$param=$request->getAttribute('param');
		$status=$request->getAttribute('status');

		$caller = new CallableCreator($handle['callback']);

		$content=$caller->getCallable()->callback($param,$request);

		$body=$this->streamFactory->createStream($content);
		$response=$this->responseFactory->createResponse($status);

		$response=$response->withBody($body);

		return $response;
	}
}