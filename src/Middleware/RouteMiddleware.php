<?php
namespace Gram\Middleware;
use Gram\Middleware\Handler\Handler;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Gram\Route\Router;

class RouteMiddleware implements MiddlewareInterface
{
	private $router,$notFoundHandler;

	public function __construct(Router $router, Handler $notFoundHandler){
		$this->router=$router;
		$this->notFoundHandler=$notFoundHandler;	//der handler der im errorfall getriggert werden soll
	}

	/// macht den request (normales routing)

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
		$uri=$request->getUri()->getPath();
		$method=$request->getMethod();

		$this->router->run($uri,$method);

		$status=$this->router->getStatus();

		//handle kann z. b. der controller als auch der 404 handle sein
		$request=$request
			->withAttribute('handle',$this->router->getHandle())
			->withAttribute('status',$status)
			->withAttribute('param',$this->router->getParam());

		//Bei Fehler, 404 oder 405
		if($status!==200){
			return $this->notFoundHandler->handle($request);	//erstelle response mit dem notfound handler
		}

		return $handler->handle($request);	//wenn alles ok handle nochmal aufrufen für die nächste middleware
	}
}