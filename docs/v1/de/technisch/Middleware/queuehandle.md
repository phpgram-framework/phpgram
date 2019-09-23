# QueueHandler

````php
<?php
namespace Gram\App;
class QueueHandler implements RequestHandlerInterface
````

- Steuert die Abfolge der Middleware

- Wird ind er App Klasse erstellt

- Die Routingmiddleware besitzt eine Method um einfach Middleware dem Stack hinzu zufügen (da die Mw diese Method nach dem Routing ebenfalls braucht)


- Die Standard Middleware (die bei jedem Request ausgeführt werden sollen) werden in der App Klasse auf dem Stack gespeichert

- Die Routegroup bzw. Route spezifischen werden nach dem Routing dem Stack hinzu gefügt (siehe [Route Middleware](routingmw.md))

## Funktionsweise

1. Bekommt den [ResponseCreator](responsehandle.md) übergeben. Dieser wird als letztes nach allen Mw ausgeführt

2. Middleware werden dem Stack hinzu gefügt (Routing Middleware wird immer hinzu gefügt)

3. Die handle Method wird von der App Klasse ausgeführt. Diese erwartet auch den Response

4. die erste Mw wird vom Stack genommen und über ihre process Method ausgeführt

5. Dabei wird der Request und die QueueHandler selber als ResponseCreatorInterface übergeben

6. Somit kann die Middleware die handle Method im QueueHandler wieder aufrufen wenn diese eine Mw weiter gehen will

7. Danach wird die nächste Mw vom Stack genommen und ausgeführt

8. Sollte keine Mw mehr auf dem Stack sein wird der [ResponseCreator](responsehandle.md) ausgeführt

9. Dieser erstellt dann den Response und gibt diesen zurück

10. Sollte in einer Mw ein Event oder Fehler aufgetreten sein muss diese selber einen Reponse zurück geben und kann somit die Rekursion unterbrechen

````php
<?php
public function handle(ServerRequestInterface $request): ResponseInterface
{
	if(count($this->stack)===0){
		return $this->previous->handle($request);
	}

	$middleware=array_shift($this->stack);	//hole das oberste element und lösche es aus dem array
	return $middleware->process($request,$this);	//führe die middleware aus
}
````


- Der QueueHandler bietet die Möglichkeit, den [ResponseCreator](responsehandle.md) bereits in den Mw auf zu rufen. Dazu:

	- z. B.: durch ``if($handler instanceof QueueHandler)``

	- somit gibt es auch keine Warning

- der ResponseCreator kann so bekommen werden: ``$handler->getPre()``

	- der Return kann so ausehen: ``return $handler->getPre()->handle($request);``

	- im Request wird dann das Attribut callable verändert, sodass nicht das callable des Routers ausgeführt wird

	- siehe [ResponseCreator](responsehandle.md)

<br>

[hier gehts weiter mit: ResponseCreator](responsehandle.md)

### Inhalt Middleware
[1. Start](index.md) <br>
[2. QueueHandler](queuehandle.md) <br>
[3. ResponseCreator](responsehandle.md) <br>
[4. Routing Middleware](routingmw.md) <br>
[5. Middleware Collector](mwcollector.md) <br>
[6. Class Middleware](classmw.md)