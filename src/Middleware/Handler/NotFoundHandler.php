<?php
namespace Gram\Middleware\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class NotFoundHandler extends CallbackHandler
{
	public function handle(ServerRequestInterface $request): ResponseInterface{
		return parent::handle($request);
	}
}