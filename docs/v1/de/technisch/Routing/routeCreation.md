# Route Creation

- es werden ein Routecollector, Route, Routegroup und Generatoren benutzt

## Route Collector
````php
<?php
class RouteCollector implements CollectorInterface
````
- Collectoren müssen ``Gram\Route\Interfaces\CollectorInterface`` implementieren

- Collector sammelt definierte Routes in Routeobjekten und Routegruppen in Route Group Objekten

- Diese Objekte bieten die Möglichkeit Middleware und Strategy der Route bzw. Gruppe hinzu zufügen

### Route adden
- Method:
````php
<?php
class RouteCollector implements CollectorInterface
{
	public function add(string $path,$handler,array $method):Route
}

````
- Die Method muss ein neues Routeobjekt erzeugen und zurück geben

- um Methoden an zugeben gibt es nochmal extra Methods die z. B. ``GET`` setzen

1. Zu dem Path (Url path) werden basepath und Gruppen prefix hinzugefügt

2. Das Handle wird in das Handle Array einsortiert an der Stelle der Routeid (für den Router)

3. Ein neues Routeobjekt wird erstellt

4. Das Routeobjekt wird in das Array mit den weiteren Routeobjekten einsortiert

5. Die Routeid wird um eins erhöht

6. das Routeobjekt wird zurück gegeben um Middleware oder Strategy hinzu zufügen

- Dieser Vorgang wird bei jedem Seitenaufruf wiederholt

### Route Objekt
````php
<?php
class Route
{
	public function __construct(
		string $path,
		$method,
		$routegroupid,
		$routeid,
		MiddlewareCollectorInterface $stack,
		StrategyCollectorInterface $strategyCollector
    )
    
    public function addMiddleware($middleware,$order=null)
    public function addStrategy($strategy)
}
````
1. Wird in der Add Method im Collector erzeugt

2. Enthält alle Informationen die die Generatoren brauchen um daraus die Routedata zu erstellen

3. Enthält zusätzlich einen Middleware und Strategy Collector, sodass nach dem adfinieren einer Route eine Middleware bzw. Strategy zur richtigen Route hinzugefügt werden kann

4. Desweiteren enthält das Routeobjekt eine create Method. Diese wird von den Generatoren aufgerufen um die Route zu parsen (nur wenn die Routes nicht gecacht wurden)

### Routegroup
````php
<?php
class RouteCollector implements CollectorInterface
{
	public function addGroup($prefix,callable $groupcollector):RouteGroup
}
````
- mehrere Routes können zu einer Gruppe mit einem Prefix zusammengefasst werden

- Middleware und Strategy die diesem Gruppen zugeordnet wird werden auch allen Routes in der Gruppe zugeordnet

- Gruppen in Gruppen sind auch möglich

- die Gruppen brauchen ein Prefix und ein callable in dem dann die Routes definiert werden

1. Das akutelle prefix wird gesichert (für nested Routes)

2. das Array mit den vorherige Gruppenids wird gesichert (um die Mw und Stategy der Gruppe zu zuordnen)

3. das akutelle Prefix wird um das neue erweitert

4. dem Gruppenid Array (das anzeigt in welchen Gruppen die Route drin ist) wird eine neue Gruppenid hinzugefügt

5. ein neues RouteGroup Objekt wird erstellt (um Mw und Strategy der Gruppe hinzu zufügen nach dem definieren, so ähnlich wie beim Route Objekt)

6. das Callable wird ausgeführt (im Callable können auch weitete Gruppen definiert werden, dann werden für diese Gruppen neue Werte erstellt, aber die alten Werte dieser Gruppe wurden bereits gespeichert)

7. Das alte Prefix und das alte Gruppenid Array wird wieder hergestellt und das Gruppen Objekt wird zurück gegeben

### Group Objekt
````php
<?php
class RouteGroup
{
	public function __construct(
		$prefix,
		$groupid,
		MiddlewareCollectorInterface $stack,
		StrategyCollectorInterface $strategyCollector
	)
}
````
- wie beim Routeobjekt werden die Collectors übergeben

- das Objekt wird nur zum hinzufügen von Mw und Strategy benutzt nicht zum speichern der Gruppen


[hier gehts weiter mit: Route Generation](routegeneration.md)

### Inhalt Routing
[1. Start](index.md) <br>
[2. Router](router.md) <br>
[3. Dispatching](dispatching.md) <br>
[4. Route Creation](routeCreation.md) <br>
[5. Route Generation](routegeneration.md)
