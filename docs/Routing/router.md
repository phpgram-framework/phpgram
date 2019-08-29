# Router
- Klasse: 
```php
<?php 
namespace Gram\Route;
class Router
{
	const REQUEST_ROUTER=1;
	const BEFORE_MIDDLEWARE=2;
	const AFTER_MIDDLEWARE=3;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const OK = 200;
	
}
```

## Routertypen
- Es werden zwei Typen angeboten
   - Middleware Router
   - Request Router

### RequestRouter

1. Router wird mit der Url und der http Methode aufgerufen (run())
2. Hole zuerst die Routes als Map (siehe Route Mapping)
3. Führe dispatch() aus
4. Diese druchläuft alle Dispatcher: den für die Static und den für die dynamischen Routes
5. Wenn ein Dispatcher Erfolg hatte (siehe Dispatching) wird der Handler der Route zurück gegeben
6. Wenn der Request Router aufgerufen wird -> prüfe Http Method. Wenn diese nicht stimmt gebe 405 aus
7. Wenn die Dispatcher nichts gefunden haben -> gebe 404 aus.

# Middleware Router

1. Funktionsweise genau wie der Request Router
2. Middleware haben auch eigene Routes die dann mehre Seiten und Gruppen gleichzeitig abdecken

[hier gehts weiter mit: Dispatching](dispatching.md)

### Inhalt Routing
[1. Start](index.md) <br>
[2. Router](router.md) <br>
[3. Dispatching](dispatching.md) <br>
[4. Route Creation](routeCreation.md) <br>
[5. Mapping](routemapping.md)
