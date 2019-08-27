<?php
namespace Gram\Middleware;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class Request implements MiddlewareInterface
{

	//macht den request (normales routing)


	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{

	}
}