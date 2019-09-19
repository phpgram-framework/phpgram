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

namespace Gram\Middleware\Handler;

use Gram\Strategy\StrategyInterface;
use Gram\CallbackCreator\CallbackCreatorInterface;
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
class ResponseHandler implements RequestHandlerInterface
{
	private $stdstrategy,$creator,$callable,$param,$request,$responseFactory,$streamFactory;

	public function __construct(
		ResponseFactoryInterface $responseFactory,
		StreamFactoryInterface $streamFactory,
		CallbackCreatorInterface $creator,
		StrategyInterface $strategy
	){
		$this->stdstrategy=$strategy;
		$this->creator=$creator;
		$this->responseFactory=$responseFactory;
		$this->streamFactory=$streamFactory;
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

		//erstelle head
		$head=$strategy->getHeader();

		//Führe Callable aus
		$content = $this->createBody($strategy,$creator);

		//erstelle Body
		if($content instanceof ServerRequestInterface){
			//Wenn der Return bereits ein Request ist
			$body = $content->getBody();
			$this->request=$content;
		}else if(is_string($content)){
			//Wenn Return ein String ist erstelle Body aus zurück gegebem String
			$body = $this->streamFactory->createStream($content);
		}else {
			//Sonst erstelle Body aus einer Resource
			$body = $this->streamFactory->createStreamFromResource($content);
		}

		//werte können auch durch Callable manipuliert worden sein
		$status = $this->request->getAttribute('status',200);
		$reason = $this->request->getAttribute('reason','');
		$header = $this->request->getAttribute('header',[]);

		$response = $this->responseFactory->createResponse($status,$reason);

		$response=$response
			->withBody($body)
			->withHeader($head["name"],$head["value"]);

		//Fügt Custom Header hinzu
		foreach ($header as $item) {
			$response=$response->withHeader($item["name"],$item['value']);
		}

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
	 * Nehme das Request Object des Callbacks entgegen, da dieses ggf. verändert wurde
	 * durch das Callback
	 *
	 * @param StrategyInterface $strategy
	 * @param CallbackCreatorInterface $creator
	 * @return mixed
	 */
	protected function createBody(StrategyInterface $strategy,CallbackCreatorInterface $creator)
	{
		$creator->createCallback($this->callable);
		$callback = $creator->getCallable();

		//Führe das Callback aus
		$result = $strategy->invoke($callback,$this->param,$this->request);

		//nehme Request entgegen falls das Callback Attribute verändert hat
		$this->request = $callback->getRequest();

		return $result;
	}
}