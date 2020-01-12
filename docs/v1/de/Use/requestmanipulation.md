# Request manipulieren

- Im Request Objekt werden Standard Attribute gesetzt die verändert werden können

- Anhand dieser Attribute wird der Response im [ResponseCreator](../technisch/Middleware/responsehandle.md) erstellt

## Die Attribute
		
- ``'callable'``

	- Das [Resolver](../technisch/Resolver/index.md), das mit dem [ResolverCreator](../technisch/ResolverCreator/index.md) erstellt wird und in der [Strategy](../technisch/Strategy/index.md) ausgeführt wird

	- Das Resolver muss immer ausgefüllt sein

- ``'param'``

	- Die Parameter mit denen das Resolver ausgeführt werden soll

	- wird vom Router nach dem [Dispatch](../technisch/Routing/dispatching.md) gesetzt

	- Standard ist ein leeres Array 

- ``'status'``

	- Der Http Status der im Header angezeigt werden soll

	- Wird ebenfalls nach dem Dispatch gesetzt

	- als Standard ist hier 200 (ok) gesetzt

- ``'strategy'``

	- Die Strategy mit der das Resolver ausgeführt werden soll

	- Als Standard wird die in [App](../technisch/App/index.md) angegebene Standard Strategy gesetzt

## Veränderungsmöglichkeiten

- Solange der Request nicht beim ResponseCreator angekommen ist können alle Attribute verändert werden


### Middleware
- jede Middleware hat vor dem ResponseCreator Zugriff auf den Request

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

use Gram\Middleware\QueueHandler;
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
		
		//Middleware Kette unterbrachen und direkt mit dem veränderten callable zum ResponseCreator gehen
		//dieser ist verfügbar im QueueHandler (siehe Middleware)
		if((!$username || !$userid) && $handler instanceof QueueHandler){
			$request = $request->withAttribute('callable',new Handler());
			$request = $request->withAttribute('status',401); 
			return $handler->getLast()->handle($request);
		}
		
		return $handler->handle($request);	//zur nächsten Middleware weiter gehen
	}
}
````

