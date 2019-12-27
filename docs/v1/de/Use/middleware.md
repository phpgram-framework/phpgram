# Middleware

- Technische Doc: [Middleware](../technisch/Middleware/index.md)

- Middleware werden nach [Psr 15](https://www.php-fig.org/psr/psr-15/) erstellt

- kann vor oder nach der eigentlichen Application ausgeführt werden

- es übernimmt Aufgaben wie:

	- Authentifizierung

	- Authorisierung

	- Loginstatus und Login

	- Caching

	- etc.

- die Application ist auch eine Middleware

- Middleware werden auf einem Stack gespeichert

- Middleware komunizieren mit mit dem Request Objekt 

## Middleware erstellen

- siehe [Middleware](../technisch/Middleware/index.md)

- Middleware müssen das Interface ``Psr\Http\Server\MiddlewareInterface`` implementiert haben

## Middleware setzen

- Es können Standard Middleware (die als erste ausgeführt werden), Routegroup und Route Middleware gesetzt werden

- Die Reihenfolge ist:

	1. Std Middleware

	2. Group Middleware

	3. Route Middleware

### Std Mw setzen

- ``App::app()->addMiddle()``

- hier wird ein ``Psr\Http\Server\MiddlewareInterface`` erwartet

- diese Mw werden vor dem Routing ausgeführt

### Routegroup

````php
<?php
use Gram\App\App;

App::app()->group("/admin",function (){
	//Route braucht keine Url wenn diese bei /admin ausgeführt werden soll
	App::app()->get("","AdminIndexHandler");
	//Normale Route
	App::app()->get("/role","RoleHandler");
	
	//Nested Groups
	//2. Group /settings
	App::app()->group("/settings",function (){
		//3. Group /dep
		App::app()->group("/dep",function (){
			//Routes
			App::app()->get("","DepIndexHandler");
			
			App::app()->get("/{id}/edit","EditHandler");
		});
	})->addMiddleware(new Auth());
	
})->addMiddleware(new Login());
````

- mit ``addMiddleware()`` lässt sich eine Middleware hinzufügen

- es lassen sich beliebig viele Middleware hinzufügen (weitere ``addMiddleware()`` dran hängen)

- zuerst wird bei ``/admin...`` die Login Middleware und bei ``/admin/settings...`` erst die Login und dann die Auth Mw

### Route 

````php
<?php
use Gram\App\App;

App::app()->get("/user/{id}","ExampleController@exampleControllerMethod")->addMiddleware(new Caching());
````
- wenn die Route gematcht wird werden alle Mw ihrer Gruppen zuerst aus geführt und danach die Caching Mw


## Middleware und Request

- jede Middleware hat Zugriff auf das Request Objekt und kann dieses verändern

- siehe [Request Manipulation](requestmanipulation.md)

## Beispiel Middleware

````php
<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class Auth implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		//Prüfungen
		
		//Errorbehandlung
		
		return $handler->handle($request);	//zur nächsten Middleware weiter gehen
	}
}
````
- Soltle in dieser Mw alles in Ordnung sein kann zur nächsten Mw weiter gegangen werden mit ``return $handler->handle($request)``

- der Request ist meist verändert

- Wenn ein Error oder Event aufgetreten ist so muss die Mw selber einen Response erstellen (dies kann auch über den ResponseCreator funktionieren (siehe [Response Manipulation](requestmanipulation.md)))
