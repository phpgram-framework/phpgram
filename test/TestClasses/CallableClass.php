<?php
namespace Gram\Test\TestClasses;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CallableClass
{
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
	{
		return "test";
	}
}