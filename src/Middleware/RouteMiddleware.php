<?php
namespace Gram\Middleware;
use Gram\Middleware\Handler\Handler;
use Gram\Middleware\Handler\QueueHandler;
use Gram\Route\Interfaces\MiddlewareCollectorInterface;
use Gram\Route\Interfaces\StrategyCollectorInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Gram\Route\Interfaces\RouterInterface as Router;

class RouteMiddleware implements MiddlewareInterface
{
	private $router,$notFoundHandler,$queueHandler,$middlewareCollector,$strategyCollector;
	private $routeid,$groupid;

	public function __construct(
		Router $router,
		Handler $notFoundHandler,
		QueueHandler $queueHandler,
		MiddlewareCollectorInterface $middlewareCollector,
		StrategyCollectorInterface $strategyCollector
	){
		$this->router=$router;
		$this->notFoundHandler=$notFoundHandler;	//der handler der im errorfall getriggert werden soll
		$this->middlewareCollector=$middlewareCollector;
		$this->strategyCollector=$strategyCollector;
		$this->queueHandler=$queueHandler;
	}

	/// macht den request (normales routing)

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
		$uri=$request->getUri()->getPath();
		$method=$request->getMethod();

		$this->router->run($uri,$method);

		$status=$this->router->getStatus();
		$handle=$this->router->getHandle();

		//handle kann z. b. der controller als auch der 404 handle sein
		$request=$request
			->withAttribute('handle',$handle)
			->withAttribute('status',$status)
			->withAttribute('param',$this->router->getParam());

		//Bei Fehler, 404 oder 405
		if($status!==200){
			return $this->notFoundHandler->handle($request);	//erstelle response mit dem notfound handler
		}

		$this->groupid=$handle['groupid'];
		$this->routeid=$handle['routeid'];

		$this->buildStack();

		return $handler->handle($request);	//wenn alles ok handle nochmal aufrufen für die nächste middleware
	}

	public function buildStack($addstd=false){
		//Füge Standard Middleware hinzu (die MW die immer ausgeführt wird)
		if($addstd===true){
			foreach ($this->middlewareCollector->getStdMiddleware() as $item) {
				$callable = (is_object($item))?$item:new $item;
				$this->queueHandler->add($callable);
			}
		}else{
			$grouMw=$this->middlewareCollector->getGroup($this->groupid);
			//Füge Routegroup Mw hinzu
			if ($grouMw!==null){
				foreach ($grouMw as $item) {
					$this->queueHandler->add($item);
				}
			}

			$routeMw = $this->middlewareCollector->getRoute($this->routeid);
			//Füge Route MW hinzu
			if($routeMw!==null){
				foreach ($routeMw as $item) {
					$this->queueHandler->add($item);
				}
			}
		}
	}
}