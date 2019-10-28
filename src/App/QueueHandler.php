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

namespace Gram\App;

use Gram\Exceptions\MiddlewareNotAllowedException;
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
 */
class QueueHandler implements RequestHandlerInterface
{
	private $stack, $last, $container;

	public function __construct(RequestHandlerInterface $last, ContainerInterface $container=null)
	{
		$this->last=$last;	//der rücksprung handler mit dem diese klasse aufgerufen wird
		$this->container = $container;
	}

	public function add($middleware)
	{
		$this->stack[]=$middleware;	//nach jedem durchlauf wird ein element vom stack genommen
	}

	public function getLast()
	{
		return $this->last;
	}

	/**
	 * Laufe "rekursiv" durch alle Middlewares durch
	 *
	 * Alle Middlewares rufen diese Function dieses Objekts (this) wieder auf.
	 *
	 * Wenn ein Event eingetreten ist wird ein anderer Handler aufgerufen und dieses Response zürck gegeben.
	 *
	 * Sonst laufe durch den ganzen Middleware stack und führe den letzten Handler aus
	 *
	 * Standard ist der @see ResponseCreator
	 *
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 * @throws \Exception
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		//wenn es keine Middleware gibt gebe das Ergebnis des handlers aus der zuletzt getriggert werden soll

		if(count($this->stack)===0){
			return $this->last->handle($request);
		}

		$middleware = \array_shift($this->stack);	//hole das oberste element und lösche es aus dem array

		//wenn ein Index für die Mw angegenen wurde, siehe im Container nach
		if($this->container!==null && \is_string($middleware)){
			if($this->container->has($middleware) === false){
				throw new MiddlewareNotAllowedException("Middleware: [$middleware] not found");
			}

			$middleware = $this->container->get($middleware);
		}

		if($middleware instanceof MiddlewareInterface === false){
			throw new MiddlewareNotAllowedException("Middleware needs to implement Psr 15 MiddlewareInterface");
		}

		return $middleware->process($request,$this);	//führe die middleware aus
	}
}