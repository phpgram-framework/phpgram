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

namespace Gram\Middleware\Handler;

use Gram\Strategy\StrategyInterface;
use Gram\ResolverCreator\ResolverCreatorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ResponseHandler
 * @package Gram\Middleware\Handler
 *
 * Ein Standard Psr 15 Handler der Requests zu Response umbaut
 *
 * Wird als letztes im QueueHandler aufgerufen
 *
 * Kann auch von anderen Klassen aufgerufen werden um einen Response zu erstellen
 */
class ResponseCreator implements RequestHandlerInterface
{
	private $stdstrategy,$creator,$callable,$param,$request,$response,$container,$responseFactory,$streamFactory;

	public function __construct(
		ResponseFactoryInterface $responseFactory,
		StreamFactoryInterface $streamFactory,
		ResolverCreatorInterface $creator,
		StrategyInterface $strategy,
		ContainerInterface $container=null
	){
		$this->stdstrategy=$strategy;
		$this->creator=$creator;
		$this->responseFactory=$responseFactory;
		$this->streamFactory=$streamFactory;
		$this->container = $container;
	}

	/**
	 * @inheritdoc
	 *
	 * Baut das Callback zusammen
	 *
	 * Erstellt den Response anhand des Requests
	 *
	 * Wird zuletzt im @see \Gram\App\QueueHandler ausgeführt
	 *
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$this->request = $request;

		//Die Attribute die für das Ausführen des callable benötigt werden
		$this->callable = $request->getAttribute('callable');
		$this->param = $request->getAttribute('param',[]);
		$strategy = $request->getAttribute('strategy',null) ?? $this->stdstrategy;
		$creator = $request->getAttribute('creator',null) ?? $this->creator;
		$status = $request->getAttribute('status',200);
		$reason = $request->getAttribute('reason','');
		$header = $request->getAttribute('header',[]);

		//erstelle Response mit den Werten von den Middleware
		$this->response = $this->responseFactory->createResponse($status,$reason);

		//erstelle head
		$head = $strategy->getHeader();

		$this->response = $this->response->withHeader($head["name"],$head["value"]);

		//Fügt Custom Header aus dem Request hinzu
		foreach ($header as $item) {
			$this->response=$this->response->withHeader($item["name"],$item['value']);
		}

		//Führe Callable aus
		$content = $this->createBody($strategy,$creator);

		//erstelle Body
		if($content instanceof ResponseInterface){
			//Wenn der Return bereits ein Response ist ist
			return $content;
		}else if(is_string($content)){
			//Wenn Return ein String ist erstelle Body aus zurück gegebem String
			$body = $this->streamFactory->createStream($content);
		}else {
			//Sonst erstelle Body aus einer Resource
			$body = $this->streamFactory->createStreamFromResource($content);
		}

		//setze Body in den Response ein
		$response = $this->response->withBody($body);

		return $response;
	}

	/**
	 * Erstelle den Content für den Body
	 *
	 * Erstelle aus dem callable ein CallbackInterface
	 *
	 * führe das CallbackInterface mit der gesetzen Strategy aus
	 *
	 * Nehme des return des Callbacks entgegen
	 *
	 * Nehme den Response des Callbacks entgegen, da diese ggf. verändert wurden
	 * durch das Callback
	 *
	 * @param StrategyInterface $strategy
	 * @param ResolverCreatorInterface $creator
	 * @return mixed
	 */
	protected function createBody(StrategyInterface $strategy, ResolverCreatorInterface $creator)
	{
		$creator->createResolver($this->callable);
		$resolver = $creator->getResolver();

		$resolver->setRequest($this->request);
		$resolver->setResponse($this->response);
		$resolver->setContainer($this->container);

		//Führe das Callback aus
		$result = $strategy->invoke($resolver,$this->param);

		//nehme Response entgegen falls das Callback Attribute verändert hat
		$this->response = $resolver->getResponse();

		return $result;
	}
}