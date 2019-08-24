# Router
- Klasse: 
```php
<?php 
namespace Gram\Lib\Route\Router;
class RouterRoute extends Router
{
	
}
```
- Der Router ist für die normalen Routes zuständig
1. Router wird mit der Url und der http Methode aufgerufen (run())
2. Hole zuerst die Routes als Map (siehe Route Mapping)
3. Rufe tryDispatch() in der Parent Class auf
4. Diese druchläuft alle Dispatcher: den für die Static und den für die dynamischen Routes
5. Wenn ein Dispatcher Erfolg hatte (siehe Dispatching) wird der Handler der Route zurück gegeben
6. Wenn der normale Router aufgerufen wird -> prüfe Http Method. Wenn diese nicht stimmt gebe Fehlermeldung aus
7. Wenn die Dispatcher nichts gefunden haben -> gebe 404 aus.
# Middleware Router
- Klasse:
```php
<?php 
namespace Gram\Lib\Route\Router;
class Routermiddle extends Router
{
	
}
```
- Der Middleware Router ist für die Middlewares zuständig
- Funktionsweise ähnlich die des normalen Routers
- siehe Middleware

[hier gehts weiter mit: Dispatching](dispatching.md)

### Inhalt Routing
[1. Start](index.md) <br>
[2. Router](router.md) <br>
[3. Dispatching](dispatching.md) <br>
[4. Route Creation](routeCreation.md) <br>
[5. Mapping](routemapping.md)
