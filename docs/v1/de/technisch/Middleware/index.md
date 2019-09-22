# Middleware


- steht zwischen dem Request und der Applikation

- Middleware werden auf einem Stack gespeichert und Rekursiv aufgerufen

- Eine Middleware muss das Interface ``Psr\Http\Server\MiddlewareInterface`` implementiert haben

- Middleware werden vom QueueHandler aufgerufen (siehe [QueueHandler](queuehandle.md))

- Middleware werden mit folgender Method aufgerufen:

```php
<?php
public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
{	
    return $handler->handle($request); //wenn kein Fehler aufgetreten ist
}
```

- Middleware kommunizieren unter einander mit dem Requestobjekt, dass sie auch verändern können

- Das ``Psr\Http\Server\RequestHandlerInterface`` ist in phpgram immer der QueueHandler

- Eine Middleware muss ein ``Psr\Http\Message\ResponseInterface`` zurück geben

- Wenn kein Fehler oder Event aufgetreten ist kann die Middleware mit ``return $handler->getPre()->handle($request);`` zur nächsten Mw gehen


- Wenn doch muss die Middleware einen eigenen Response erzeugen, der dann ausgegeben wird

- Dafür kann in phpgram der [ResponseHandler](responsehandle.md) so manipluiert werden, dass dieser den Response der Mw erstellt, anstatt den des Routers

## Phpgram Middleware

- phpgram hat von beginn an folgende Mw: [Routing Middleware](routingmw.md) und [Class Middleware](classmw.md)

- weitere können mit den oben angegebem Interface durch den [Middleware Collector](mwcollector.md) dem Stack hinzu gefügt werden

## Phpgram Handler

- Handler werden dafür genutzt Response Objekte zu erstellen

- [QueueHandler](queuehandle.md)

- [ResponseHandler](responsehandle.md)

- NotFoundHandler: 

	 - wird aufgerufen wenn der Router 404 oder 405 zurück gibt. Erbt vom ResponseHandler und erstellt einen Response mit dem in den Routes definierten 404 bzw. 405 Handle

<br>

[hier gehts weiter mit: QueueHandler](queuehandle.md)

### Inhalt Middleware
[1. Start](index.md) <br>
[2. QueueHandler](queuehandle.md) <br>
[3. ResponseHandler](responsehandle.md) <br>
[4. Routing Middleware](routingmw.md) <br>
[5. Middleware Collector](mwcollector.md) <br>
[6. Class Middleware](classmw.md)
