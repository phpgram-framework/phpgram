# Request manipulieren

- Im Request Objekt werden Standard Attribute gesetzt die verändert werden können

- Anhand dieser Attribute wird der Response im [ResponseHandler](../technisch/Middleware/responsehandle.md) erstellt

## Die Attribute
		
- ``'callable'``

	- Das [Callback](../technisch/Callback/index.md), das mit dem [CallbackCreator](../technisch/CallbackCreator/index.md) erstellt wird und in der [Strategy](../technisch/Strategy/index.md) ausgeführt wird

	- Das Callback muss immer ausgefüllt sein

- ``'param'``

	- Die Parameter mit denen das Callback ausgeführt werden soll

	- wird vom Router nach dem [Dispatch](../technisch/Routing/dispatching.md) gesetzt

	- Standard ist ein leeres Array 

- ``'status'``

	- Der Http Status der im Header angezeigt werden soll

	- Wird ebenfalls nach dem Dispatch gesetzt

	- als Standard ist hier 200 (ok) gesetzt

- ``'reason'``

	- Der Grund für den Status. Z. B.: Status 200; Grund OK oder 404 Grund Not Found

	- Wenn kein Wert gesetzt wurde ``''`` wird im Response Objekt ein Standardwert für den Status gesetzt

	- Wird nicht im Router gesetzt

- ``'header'``

	- Custom Header die ebenfalls ausgegeben werden sollen

	- Als Standard wird hier ein leeres Array gesetzt

	- Die Header müssen folgendes Format haben

		- ``["name"=>"Header-Name","value"=>"Wert des Headers"]``

		- die einzelnen Header müssen in einem Array zusammengefasst werden

		- ``[Header1,Header2]`` 

		- Bsp.: 

			- ``[["name"=>"Content-Length","value"=>3000],["name"=>"Header-Zwei","value"=>2]]``

- ``'strategy'``

	- Die Strategy mit der das Callback ausgeführt werden soll

	- Als Standard wird die in [App](../technisch/App/index.md) angegebene Standard Strategy gesetzt

- ``'creator'``
	
	- Der CallbackCreator mit dem das ``callable`` zu einem Callback, mit Mustererkennung, umgeformt werden soll
	
	- Als Standard wird der in App als Standard gesetzte Creator ausgeführt

## Veränderungsmöglichkeiten

- Solange der Request nicht beim ResponseHandler angekommen ist können alle Attribute verändert werden

- In dem Handler der Route (siehe [Route Example](route.md)) z. B. Klassen oder Controller können: ``status``, ``header`` und ``reason`` verändert werden.

- Die anderen Attribute können nicht verändert werden, da der Handler mit diesen ausgeführt wurde

### Middleware
- jede Middleware hat vor dem ResponseHandler Zugriff auf den Request

- Die Middleware kann z. B. das callable und den Status austauschen wenn in der Middleware ein Fehler aufgetreten ist (z. B. nicht eingelogt). Die werden dann ausgeführt, an statt der Handler der Route (siehe [Middleware](middleware.md))

### Route
- die Routing Middleware setzt den gefunden Handler in callable ein

- und setzt den Status

- Die Strategy kann in der Route ebenfalls überschrieben werden (siehe [Strategy](strategy.md))

- Die Parameter (param) werden, sollte es eine Wildcard Route sein ebenfalls von der Routing Middleware gesetzt

## How To Do

````php
<?php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Auth
 * Verändert den Request
 */
class Auth implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		//Prüfungen
		
		$request = $request->withAttribute('callable',new Handler());	//hier wird eine neue Klasse eingesetzt
		$request = $request->withAttribute('status',401); //nicht authorisiert
		return $handler->handle($request);	//zur nächsten Middleware weiter gehen
	}
}

use Gram\App\QueueHandler;
/**
 * Class AuthInterrupt
 * Verändert den Request und unterbricht die Middleware Kette (siehe Middleware)
 */
class AuthInterrupt implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		//Attribute aus vorherigen Middleware
		$username = $request->getAttribute('username',false);
		$userid= $request->getAttribute('userid',false);
		
		//Middleware Kette unterbrachen und direkt mit dem veränderten callable zum ResponseHandler gehen
		//dieser ist verfügbar im QueueHandler (siehe Middleware)
		if((!$username || !$userid) && $handler instanceof QueueHandler){
			$request = $request->withAttribute('callable',new Handler());
			$request = $request->withAttribute('status',401); 
			return $handler->getPre()->handle($request);
		}
		
		return $handler->handle($request);	//zur nächsten Middleware weiter gehen
	}
}
````

