<?php
/**
 * phpgram
 *
 * This File is part of the phpgram Micro Framework
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

namespace Gram\Middleware;

use Gram\Exceptions\MiddlewareNotAllowedException;
use Gram\Middleware\Queue\QueueInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class QueueHandler
 * @package Gram\App
 *
 * Verwaltet die Middleware
 *
 * Wenn eine Middleware weiter gegangen werden soll wird diese Handle Funktion wieder aufgerufen
 *
 * Führt Psr 15 und callable Middleware aus
 */
class QueueHandler implements QueueHandlerInterface
{
	/** @var RequestHandlerInterface */
	protected $last;

	/** @var ContainerInterface */
	protected $container;

	public function __construct(RequestHandlerInterface $last, ContainerInterface $container=null)
	{
		$this->last = $last;	//der rücksprung handler mit dem diese klasse aufgerufen wird
		$this->container = $container;
	}

	/**
	 * @inheritdoc
	 * @throws MiddlewareNotAllowedException
	 */
	public function add(ServerRequestInterface $request, $middleware)
	{
		/** @var QueueInterface $queue */
		$queue = $this->getQueue($request);

		$queue->add($middleware);
	}

	/**
	 * @inheritdoc
	 */
	public function getLast():RequestHandlerInterface
	{
		return $this->last;
	}

	/**
	 * @inheritdoc
	 * @throws MiddlewareNotAllowedException
	 */
	public function getQueue(ServerRequestInterface $request):QueueInterface
	{
		/** @var QueueInterface $queue */
		$queue = $request->getAttribute(QueueInterface::class);

		if($queue === null){
			throw new MiddlewareNotAllowedException("No Queue Object found");
		}

		return $queue;
	}

	/**
	 * @inheritdoc
	 *
	 * Wenn ein Event eingetreten ist wird ein anderer Handler aufgerufen und dieses Response zürck gegeben.
	 *
	 * Sonst laufe durch den ganzen Middleware stack und führe den letzten Handler aus
	 *
	 * Standard ist der @see ResponseCreator
	 *
	 * Laufe "rekursiv" durch alle Middlewares durch
	 *
	 * Alle Middlewares rufen die Method @see handle() dieses Objekts (this) wieder auf.
	 *
	 * @throws \Exception
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$middleware = $this->getQueue($request)->next();	//hole die nächste Middleware für den Request

		if ($middleware === false) {
			return $this->last->handle($request);
		}

		return $this->executeMiddleware($request,$middleware);
	}

	/**
	 * @inheritdoc
	 * 
	 * Ruft einfach die handle function auf
	 * 
	 * @throws \Exception
	 */
	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		return $this->handle($request);
	}

	/**
	 * Führe die Middleware aus
	 *
	 * Entweder als Psr 15 Middleware oder als Callable
	 *
	 * @param ServerRequestInterface $request
	 * @param $middleware
	 * @return ResponseInterface
	 * @throws MiddlewareNotAllowedException
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	protected function executeMiddleware(ServerRequestInterface $request, $middleware):ResponseInterface
	{
		//wenn ein Index für die Mw angegenen wurde, siehe im Container nach
		if ($this->container !== null && \is_string($middleware)) {
			$middleware = $this->container->get($middleware);
		}

		if ($middleware instanceof MiddlewareInterface) {
			return $middleware->process($request,$this);
		}

		if(\is_callable($middleware)) {
			return $middleware($request,$this);
		}

		throw new MiddlewareNotAllowedException("Middleware needs to implement Psr 15 MiddlewareInterface or from type Callable!");
	}
}