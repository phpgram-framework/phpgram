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

class GroupCountBased extends Generator
````
- eine abstract class die die Static Routes erzeugt (da das bei jedem Generator gleich sein wird)

- eine Klasse die von der abstract erbt und die dynamic Routes erzeugt (hier kann jeder beliebige Generator genutzt werden)

- hier werden die Routes nach dem Group Count Based Prinzip generiert von [FastRoute](http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html)

- der GroupCountBased wird aufgerufen mit einer Method im Generator

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

3. Jede der Routes wird durchlaufen

4. Beim Durchlauf parse die Routes (mithilfe des Parser Objekts in dem Route Objekt)

5. Prüfe ob die Route Parameter besitzt (dynamic)

6. Wenn ja ordne diese in ein Array ein, wenn nein füge sie dem static Teil der Routemap hinzu

7. Um die dynamic Routes zu verarbeiten rufe die Method ``generateDynamic()`` mit den zuvor gesammelten Routes aus

````php
<?php
//zu 6.
$this->static[$route->path]=$route->handle;
````

### Dynamic Routes

````php
<?php
class GroupCountBased extends Generator
{
	public function generateDynamic(array $routes)	
}
````

1. Dort werden die Routes gesammelt bis eine Anzahl an Routes erreicht ist

2. die Anzahl wird errechnet: durch einen fest vorgegebenen Wert und der Anzahl der Routes (wie viele Chunks lassen sich mit der Anzahl an Routes erstellen und um wie vielt muss der Chunk erweitert werden damit alle Chunks voll sind)

3. Füge Platzhalter hinzu um die Position der Route in der Regex zu bestimmten
	
	1. Wenn die Route an zweiter Stelle steht und keine Platzhalter besitzt werden ihr drei hinzu gefügt (da die Route an 2. Position in der Regex steht und die Matches gezählt werden, im ersten Match ist immer der komplette String drin deswegen position +1)
	
	2. hat die Route Parameter wird die Anzahl der parameter von der Anzahl der benötigten Platzhalter abgezogen ``$routeCollector[]= $path.str_repeat('()', $number - $varcount);``

4. Wenn die Anzahl erreicht ist werden die Routes zu einem Chunk zusammengefasst

5. Dazu werden die gesammelten Routes zu einer Regex zusammengefasst ``$routeList[] = '~^(?|'.implode('|',$routeCollector).')$~x';``

6. Das Handle der Route steht dann an dem Index entsprechend der Anzahl an Platzhaltern (sodass das Handle beim Dispatcher leicht wiedergefunden werden kann)

### Output
- Zum Schluss werden die generierten Routes in einer Map zurück gegeben die aus
	
	- den Static und
	
	- dynamic Routes besteht

### Inhalt Routing
[1. Start](index.md) <br>
[2. Router](router.md) <br>
[3. Dispatching](dispatching.md) <br>
[4. Route Creation](routeCreation.md) <br>
[5. Route Generation](routegeneration.md)