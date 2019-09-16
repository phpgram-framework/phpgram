# Dispatcher
- Dispatcher müssen das Interface ``Gram\Route\Interfaces\DispatcherInterface`` implementiert haben
- Es gibt eine abstract Klasse von der die Dispatcher erben können um die Static Routes zu durchsuchen (da der Vorgang bei jedem Dispatcher gleich sein wird)
- Für die dynamic Routes können unterschiedliche Dispatcher genutzt werden
- hier wird der Group Count Based Dispatcher von [FastRoute](http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html) verwendet

````php
<?php
abstract class Dispatcher implements DispatcherInterface

class DynamicDispatcher extends Dispatcher
````

## Dispatcher allgemein
- erhalten Routes die durchsucht werden sollen vom Router übergeben (dieser erhält sie vom Route Collector)
- Für Aufbau der Daten siehe [Route Creation](routeCreation.md)

## Static Routes
1. Prüfe im static Route Teil der Daten ob die die Url als Index dort gesetzt ist
2. Wenn ja gebe ``DispatcherInterface::FOUND`` und die weiteren Route Daten zurück. Wenn nichts gefunden rufe den Dynamic Diaptcher auf
- Static erfolgt in der abstract Class, Dynamic im Dynamic Dispatcher der davon erbt und auch aufgerufen wird

## Dynamic Routes
1. Überprüfe die zusammengesetzen Regex (den Chunk) ``preg_match($regex,$uri,$matches)``
2. Wenn die Route nicht in der Regex war prüfe die nächste Regex
3. Wenn etwas gefunden zähle die Matches ``$route = $handler[$i][count($matches)];``. Die Anzahl der Matches ist dann die Stelle in dem Handle Array an der der Handle für diese Route steht (und die Nummer, des Chunk, der aktuell geprüften Regex)
4. Die Routeparameter werden mit den Werten aus der Url gefüllt
5. Zum Schluss wird dann der Status und ggf. der Route Handle sowie die Parameter zurück gegeben
````php
<?php
//Zu 4.:

$var=[];
foreach ($route[1] as $j=>$item) {
	$var[$item]=$matches[$j+1];
}
````

<br>

[hier gehts weiter mit: Route Creation](routeCreation.md)

### Inhalt Routing
[1. Start](index.md) <br>
[2. Router](router.md) <br>
[3. Dispatching](dispatching.md) <br>
[4. Route Creation](routeCreation.md) <br>
[5. Route Generation](routegeneration.md)