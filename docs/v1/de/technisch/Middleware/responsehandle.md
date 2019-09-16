# ResponseHandler

````php
<?php
namespace Gram\Middleware\Handler;
class ResponseHandler implements RequestHandlerInterface
````
- erstellt den Response mit Header und Body
- wird in der App Klasse erstellt
- wird als letztes Element im Mw Stack ausgeführt
- kann dank es [QueueHandler](queuehandle.md) von anderen Middleware ausgeführt werden
- bekommt Factories für Response und Stream übergeben
- bekommt einen CallbackCreatorInterface und StrategyInterface übergeben die ausgeführt werden sollte nichts anderes im request übergeben worden sein

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
	1. ``$request->getAttribute('reason','');`` welcher Grund soll für den Status angegeben werden (kann auch leer sein dann wird der Default Grund gesetzt)
	1. ``$request->getAttribute('header',[])`` weitere Header, müssen folgendes Muster enthalten header['name'] und header['value']
	1. ``$request->getAttribute('strategy',null) ?? $this->stdstrategy;`` die Strategy wie der Output des callable zu behandeln ist (z. B. json encode oder Buffern), wenn nichts gesetzt: die Standard Strategy
	1. ``$request->getAttribute('creator',null) ?? $this->creator;`` wie das callable erstellt werden kann und wie es ausgeführt werden kann (wenn nichts gesetzt führe den Standard aus)
2. hole von der Strategy den ``content-typ``
3. erstelle den Inhalt des Stream mithilfe des Callback Creators und der Strategy
	1. ``$creator->createCallback($this->callable);`` wandle das callable aus dem Request in ein richtiges um
	1. ``$strategy->invoke($creator->getCallable(),$this->param,$this->request);`` rufe die Strategy mit dem umgewandelten Callback und den benötigen Parametern (die Route Parameter und der Request)
	1. gebe den return der Strategy wieder zurück und setze den als Content für den Stream
4. erstelle mit dem Content den Stream mithilfe der Stream Factory
5. erstelle den Response mithilfe der Response Factory, mit dem übergeben Status und Reason
6. setze die gesammelten Header in den Response ein
7. setze den Stream als Body in den response ein
8. gebe den fertigen Response zurück

## Manipulation des ResponseHandlers
- alle Attribute die in 1. aufgelistet wurden können von den Middleware verändert werden
- param, status, reason, header strategy und creator haben Standardwerte
- es muss immer ein callable gesetzt sein
- callable kann alles sein was der [Creator](../CallbackCreator/index.md) (ob Standard oder übergebener, eigens definierter) zu einem [CallabackInterface](../Callback/index.md) umbauen kann
- normalerweise füllt die [Routing Middleware](routingmw.md) param, status und callable aus und für creator und strategy werden Standard in der Appklasse definiert

<br>

[hier gehts weiter mit: Routing Middleware](routingmw.md)

### Inhalt Middleware
[1. Start](index.md) <br>
[2. QueueHandler](queuehandle.md) <br>
[3. ResponseHandler](responsehandle.md) <br>
[4. Routing Middleware](routingmw.md) <br>
[5. Middleware Collector](mwcollector.md) <br>
[6. Controller Middleware](controllermw.md)