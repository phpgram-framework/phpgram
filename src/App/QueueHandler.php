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
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\App;

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
	private $stack, $previous;

	public function __construct(RequestHandlerInterface $previous)
	{
		$this->previous=$previous;	//der rücksprung handler mit dem diese klasse aufgerufen wird
	}

	public function add(MiddlewareInterface $middleware)
	{
		$this->stack[]=$middleware;	//nach jedem durchlauf wird ein element vom stack genommen
	}

	public function getPre()
	{
		return $this->previous;
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
	 * hier ist das der @see ResponseHandler
	 *
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		//wenn es keine Middleware gibt gebe das Ergebnis des handlers aus der zuletzt getriggert werden soll

		if(count($this->stack)===0){
			return $this->previous->handle($request);
		}

		$middleware=array_shift($this->stack);	//hole das oberste element und lösche es aus dem array
		return $middleware->process($request,$this);	//führe die middleware aus
	}
}