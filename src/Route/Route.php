<?php
namespace Gram\Route;
use Gram\Route\Router\RouterRoute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class Route
 * @package Gram\Route
 * @author Jörn Heinemann
 */
class Route implements MiddlewareInterface
{
	private $options;

	public function __construct($options){
		$this->options=$options;
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
		$httpMethod = $request->getMethod();
		$uri = $request->getUri()->getPath();

		$router=new RouterRoute($this->options);
		$router->run($uri,$httpMethod);

		$status=$router->getStatus();
		$handle=$router->getHandle();
		$param=$router->getParam();

		$request=$request->withAttribute("handle",$handle);
		$request=$request->withAttribute("param",$param);
		$request=$request->withAttribute("status",$status);

		return $handler->handle($request);
	}
}