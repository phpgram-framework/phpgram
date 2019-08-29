<?php
namespace Gram\Middleware\Handler;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

abstract class Handler implements RequestHandlerInterface
{
	protected $responseFactory,$streamFactory;

	public function __construct(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory){
		$this->responseFactory=$responseFactory;
		$this->streamFactory=$streamFactory;
	}

}