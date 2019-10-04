<?php
namespace Gram\Test\Middleware\Handler;


use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DummyLastHandler implements RequestHandlerInterface
{


	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$psr17 = new Psr17Factory();

		$stream = $psr17->createStream('Ein Stream');

		$words = $request->getAttribute('words',[]);

		foreach ($words as $word) {
			$stream->write($word);
		}

		$response = $psr17->createResponse();

		return $response->withBody($stream);
	}
}