# Route generieren 
````php
<?php
class RouteCollector implements CollectorInterface
{
	public function getData()
}
````
- diese Funktion liefert dem Router die Daten für den Dispatcher
- sollte eine Cache Datei vorliegen (wird über den Construktor übergeben) wird diese geladen
- sonst werden die Routes generiert

## Generator
- Generatoren müssen das Interface ``Gram\Route\Interfaces\GeneratorInterface`` implementieren

````php
<?php
abstract class Generator implements GeneratorInterface

class DynamicGenerator extends Generator
````
- eine abstract class die die Static Routes erzeugt (da das bei jedem Generator gleich sein wird)
- eine Klasse die von der abstract erbt und die dynamic Routes erzeugt (hier kann jeder beliebige Generator genutzt werden)
- hier werden die Routes nach dem Group Count Based Prinzip generiert von [FastRoute](http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html)
- der DynamicGenerator wird aufgerufen mit einer Method im Generator

### Static Routes
````php
<?php
abstract class Generator implements GeneratorInterface
{
	public function generate(array $routes)
}
````
1. die Funktion generate wird mit allen vom Collector gesammelten Routes aufgerufen
2. in dem Array befinden sich die Routeobjekte
3. 


### Inhalt Routing
[1. Start](index.md) <br>
[2. Router](router.md) <br>
[3. Dispatching](dispatching.md) <br>
[4. Route Creation](routeCreation.md) <br>
[5. Route Generation](routegeneration.md)