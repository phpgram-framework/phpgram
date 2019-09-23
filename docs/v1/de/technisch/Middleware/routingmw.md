# RoutingMiddleware

````php
<?php
namespace Gram\Middleware;
class RouteMiddleware implements MiddlewareInterface
````
- Diese Mw ist standardgemäß eingebunden


1. Mw startet den Router mit dem Url Path aus dem Request Objekt

2. nachdem Routeprocess (siehe [Routing](../Routing/index.md)) werden der gefundene Handler, Routestatus und die Route Parameter in den Request gespeichert

3. Sollte der Status des Routers nicht 200 sein (404 oder 405) wird der NotFoundHandler aufgerufen

4. Die Route Group Nummer sowie die Route Id werden ermittelt

5. Prüfe zuerst ob eine Strategy für die Route gesetzt wurde (über den Strategycollector) und füge diese dem Request als Attribut hinzu

6. Wenn nicht prüfe alle Routegroups durch in denen die Route drin ist ob hier eine Strategy gesetzt wurde, wenn ja wird diese genommen (immer die letzte Strategy wird genommen)

7. Danach wird der Stack mit den Middleware erweitert
	
	1. Im [Middleware Collector](mwcollector.md) werden alle Middleware der Groups in denen die Route drin ist hinzugefügt (angefangen bei der ersten)
	
	2. Danach die Middleware der Route
	
	3. Es können jeweils mehrere Middleware auf einmal definiert werden
	
	4. Die Mw werden dann über den [QueueHandler](queuehandle.md) hinzugefügt

8. danach wird der [QueueHandler](queuehandle.md) wieder augerfuen ``return $handler->handle($request);`` mit dem veränderten Request 

<br>

[hier gehts weiter mit: Middleware Collector](mwcollector.md)

### Inhalt Middleware
[1. Start](index.md) <br>
[2. QueueHandler](queuehandle.md) <br>
[3. ResponseCreator](responsehandle.md) <br>
[4. Routing Middleware](routingmw.md) <br>
[5. Middleware Collector](mwcollector.md) <br>
[6. Class Middleware](classmw.md)