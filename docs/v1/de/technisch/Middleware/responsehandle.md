# ResponseCreator

````php
<?php
namespace Gram\Middleware\Handler;
class ResponseCreator implements RequestHandlerInterface
````

- erstellt den Response mit Header und Body

- wird in der [App Klasse](../App/index.md) erstellt

- wird als letztes Element im Mw Stack ausgeführt

- kann dank es [QueueHandler](queuehandle.md) von anderen Middleware ausgeführt werden um einen anderen Response zu erstellen sollt ein Event eingetroffen sein

- bekommt Psr 17 Factory für Response übergeben

- bekommt einen ResolverCreatorInterface und StrategyInterface übergeben, die ausgeführt werden, sollte nichts anderes, im request übergeben worden sein

## Funktionsweise

````php
<?php
public function handle(ServerRequestInterface $request): ResponseInterface
{
	
}
````

1. hole aus dem Request alle nötigen Informationen um den Response zu erstellen:

	1. ``$request->getAttribute('callable');`` was für den Body ausgeführt werden soll

	1. ``$request->getAttribute('param',[]);`` mit welchen Parametern soll das callable ausgeführt werden

	1. ``$request->getAttribute('status',200);`` welcher Status soll im Head stehen

	1. ``$request->getAttribute('strategy',null) ?? $this->stdstrategy;`` die Strategy wie der Output des callable zu behandeln ist (z. B. json encode oder Buffern), wenn nichts gesetzt: die Standard Strategy

2. Erstelle den Response auf Basis der im Request übergebenen Attribute mithilfe der Psr 17 ResponseFactory

3. Lasse mithilfe des ResolverCreaters aus dem Callable einen Resolver erstellen (z. B. Class, Function etc.) und übergebe den Psr 11 Container an den Resolver (Request und Response werden von der Strategy gesetzt)

4. Rufe die Strategy mit dem Resolver, Route parameter, Request und Response auf 

5. Die Strategy führt den Resolver aus und gibt einen Response wieder zurück

## Manipulation des ResponseCreators

- alle Attribute die in 1. aufgelistet wurden können von den Middleware verändert werden

- param, status, reason, header strategy und creator haben Standardwerte

- es muss immer ein callable gesetzt sein

- callable kann alles sein was der [Creator](../ResolverCreator/index.md) (ob Standard oder übergebener, eigens definierter) zu einem [CallabackInterface](../Resolver/index.md) umbauen kann

- normalerweise füllt die [Routing Middleware](routingmw.md) param, status und callable aus und für creator und strategy werden Standard in der Appklasse definiert

- Das ausgeführte Callable kann zudem noch den Response direkt verändern und auch einn eigenen erstellen zun zurück geben

<br>

[hier gehts weiter mit: Routing Middleware](routingmw.md)

### Inhalt Middleware
[1. Start](index.md) <br>
[2. QueueHandler](queuehandle.md) <br>
[3. ResponseCreator](responsehandle.md) <br>
[4. Routing Middleware](routingmw.md) <br>
[5. Middleware Collector](mwcollector.md) <br>
[6. Class Middleware](classmw.md)