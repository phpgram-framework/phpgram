<?php
namespace Gram\Middleware\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NotFoundHandler implements RequestHandlerInterface
{
	private $callbackHandler;

	public function __construct(ResponseHandler $callbackHandler)
	{
		$this->callbackHandler=$callbackHandler;
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return $this->callbackHandler->handle($request);
	}
}