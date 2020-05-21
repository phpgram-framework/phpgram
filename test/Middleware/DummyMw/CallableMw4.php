<?php
namespace Gram\Test\Middleware\DummyMw;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CallableMw4
{
	public function __invoke(ServerRequestInterface $request, callable $next)
	{
		$request = $request->withAttribute('callable',"callableMws");

		/** @var ResponseInterface $response */
		$response = $next($request);

		$response->getBody()->write(" at the end: mw3");

		return $response;
	}
}