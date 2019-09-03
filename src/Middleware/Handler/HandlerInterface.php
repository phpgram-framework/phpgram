<?php
namespace Gram\Middleware\Handler;
use Psr\Http\Message\ServerRequestInterface;

interface HandlerInterface
{
	public function handle(ServerRequestInterface $request);
}