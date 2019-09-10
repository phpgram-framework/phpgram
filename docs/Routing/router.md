# Router
- Klasse: 
```php
<?php 
namespace Gram\Route;
class Router implements RouterInterface
{
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const OK = 200;
}
```
- Router müssen ``Gram\Route\Interfaces\RouterInterface`` implementieren

## Vorbereitung
- Routes müssen vorher definiert sein (siehe [4. Route Creation](routeCreation.md))


## Funktionsweise
- Starte die run Method mit der Request Url und der Request Method
````php
<?php
public function run($uri,$httpMethod=null)
````
1. Die Url wird dekodiert (zwecks Umlauten etc.)
2. Übergebe die Werte des Collectors an den Dispatcher
3. Der Dispatcher wird mit der Url aufgerufen und gibt ein Handle zurück, dass in den Routes definiert wurde und den Routestatus (siehe [Dispatcher](dispatching.md))
4. Sollte die Route nicht gefunden worden sein wird der 404 Handle erstellt
5. Die Method wird überprüft, soltle diese nicht mit der Route übereinstimmten wird der 405 Handle ausgegeben
6. Sonst nehme den Handle vom Collector für die gefundene Route entgegen
7. Speichere die gefunden Parameter der Route ab (solle es eine dynamic Route sein)
8. Im Router kann dann auf die gefunden Varaiblen und Parameter zugegriffen werden

[hier gehts weiter mit: Dispatcher](dispatching.md)

### Inhalt Routing
[1. Start](index.md) <br>
[2. Router](router.md) <br>
[3. Dispatching](dispatching.md) <br>
[4. Route Creation](routeCreation.md) <br>
[5. Route Generation](routegeneration.md)