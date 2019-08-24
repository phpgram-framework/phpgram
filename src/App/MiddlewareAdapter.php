<?php
namespace Gram\App;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareAdapter implements MiddlewareInterface
{

	public function __construct(){

	}

	/**
	 * @inheritdoc
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
		$body=$handler->handle($request);	//der Handler hat bereits den Middleware Router übergeben bekommen

	}
}