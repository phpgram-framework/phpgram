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

- bekommt Factories für Response und Stream übergeben

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

2. hole von der Strategy den ``content-typ``

3. Erstelle den Response auf Basis der im Request übergebenen Attribute mithilfe der Psr 17 ResponseFactory

4. setze die gesammelten Header in den Response ein

5. erstelle den Inhalt des Stream mithilfe des Resolver Creators und der Strategy

	1. ``$creator->createResolver($this->callable);`` wandle das callable aus dem Request in ein richtiges um
	
	1. Übergebe dem Resolver Request, Response und Container Objects, damit dieser die Objects weiter an die Application geben kann

	1. ``$strategy->invoke($creator->getCallable(),$this->param);`` rufe die Strategy mit dem umgewandelten Resolver und den benötigen Parametern (die Route Parameter)

	1. Nehme nachdem die Application fertig ist den Response wieder entgegen. Diesen könnte die App verändert haben
	
	1. gebe den return der Strategy wieder zurück und setze den als Content für den Stream

6. erstelle mit dem Content den Stream mithilfe der Stream Factory. Sollte der Content bereits ein ResponseInterface sein (z. B. wenn die App einen eigenen Response zurück gibt) wird dieses Object zurück gegeben

7. setze den Stream als Body in den response ein

8. gebe den fertigen Response zurück

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