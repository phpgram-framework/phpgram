# Route Mapping
- Klasse: 
```php
<?php 
namespace Gram\Lib\Route\Map;
class RouteMap extends Map{
	
}
```
- Eine Map wird dann an den Dispatcher übergeben damit dieser weiß welche Routes mit welchen handlern geroutet werden sollen
- Es gibt eine Map für die normalen Routes und eine für die Middlewares (beide funktionieren ähnlich)
1. Die Mapklasse wird mit getMap() aufgerufen
2. der Router ruft RouteMap auf der Middleware Router ruft MiddlewareMap auf
3. Beide Klassen erben von Map die auch die meisten Funktionen besitzt
4. der Dispatcher erwartet eine Map Klasse
5. Wenn getMap() aufgerufen wird, prüfe zuerst ob es einen Cache gibt (bzw. ob gecacht werden soll)
6. Wenn es einen Cache gibt lade die Cachedatei
7. Wenn nicht erstelle die Map (siehe Route Creation) und speichere danach die Map als Cache
8. Gebe die Map zurück

<br>

[hier gehts zurück: Start](index.md)

### Inhalt Routing
[1. Start](index.md) <br>
[2. Router](router.md) <br>
[3. Dispatching](dispatching.md) <br>
[4. Route Creation](routeCreation.md) <br>
[5. Mapping](routemapping.md)
