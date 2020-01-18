# Getting Started

## Hello World

````php
<?php

use Gram\App\App;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

App::app()->get("/",function (){
	return "Hello World";
});

$psr17Factory = new Psr17Factory();

$creator = new ServerRequestCreator($psr17Factory,$psr17Factory,$psr17Factory,$psr17Factory);
$request = $creator->fromGlobals();

//Psr 17 Response Factory
App::app()->setFactory($psr17Factory);

//emit the Response
App::app()->start($request);
````

- if `/` is matched its print out "Hello World"

- To start the App needs a Psr 17 Response Factory and a Psr 7 Request Object

- in this case with Nyholm Psr 7

# Define Routes

## Route Handler

- Closure (anonymous Function)

````php
<?php

use Gram\App\App;

App::app()->get("/function",function (){
	return "Hello World";
});
````

- Class (an object will be created (incl dependency injection) by matching this route)

````php
<?php

use Gram\App\App;

App::app()->get("/class",IndexClass::class."@index");
````

- Callable: from type callable (function or object with method __invoke() )

````php
<?php
use Gram\App\App;

App::app()->get("/class",new CallableObject());
````

## Http Methods

- this routes will only matched if the http methods is the same

- GET

````php
<?php
use Gram\App\App;

//Will be matched if the http Method is GET
App::app()->get("/function",function (){
	return "Hello World";
});
````
- POST

````php
<?php
use Gram\App\App;

App::app()->post("/function",function (){
	return "Hello World";
});
````

- GET or POST

````php
<?php
use Gram\App\App;

//matched if method is GET or POST
App::app()->getpost("/function",function (){
	return "Hello World";
});
````
- PATCH

````php
<?php
use Gram\App\App;

App::app()->patch("/function",function (){
	return "Hello World";
});
````

- PUT

````php
<?php
use Gram\App\App;

App::app()->put("/function",function (){
	return "Hello World";
});
````

- DELETE

````php
<?php
use Gram\App\App;

App::app()->delete("/function",function (){
	return "Hello World";
});
````

- HEAD

````php
<?php
use Gram\App\App;

App::app()->head("/function",function (){
	return "Hello World";
});
````
- OPTIONS

````php
<?php
use Gram\App\App;

App::app()->options("/function",function (){
	return "Hello World";
});
````
- any Method

````php
<?php
use Gram\App\App;

App::app()->any("/function",function (){
	return "Hello World";
});
````

- self defined

````php
<?php
use Gram\App\App;

App::app()->add("/function",function (){
	return "Hello World";
}, ['get','post','put']);
````

## Wildcard Routes

- routes with variable values

- In the example below: 
	- parse any value for id to the callable, $id will be the value of id in the route
	- `/function/123` -> ID = 123
	- `/function/abc` -> ID = abc
	- `/function/` -> wont match
	- same with classes

````php
<?php
use Gram\App\App;

App::app()->get("/function/{id}",function ($id){
	return "ID = ".$id;
});
````

- use regex to specify the match 
	- default: match everything until the next `/`
	- in this case: only match Integer for id

````php
<?php
use Gram\App\App;

App::app()->get("/function/{id:\d+}",function ($id){
	return "ID = ".$id;
});
````

- pre defined types: 
	- n = Integer (numeric)
	- a = alphanumeric
	- all = everything even the `/`

````php
<?php
use Gram\App\App;

App::app()->get("/function/{id:n}",function ($id){
	return "ID = ".$id;
});
````

- self defined types

````php
<?php
use Gram\App\App;

//self defined types: 
use Gram\Route\Parser\StdParser;

StdParser::addDataTyp('lang','de|en');
StdParser::addDataTyp('otherLang','fr|ru|es');

//only match if id = de or en
App::app()->get("/function/{id:lang}",function ($id){
	return "ID = ".$id;
});
````

- Route Groups
	- define a prefix which will be added to all route path inside the group
	- and a callable, this will be executed to collect the routes inside the group

````php
<?php
use Gram\App\App;

App::app()->group("/prefix",function (){
	App::app()->get("/function/{id:lang}",function ($id){
    	return "ID = ".$id;
    });
	
	//nested groups
	App::app()->group("/prefix2",function (){
		//...
	});
});

````

## Middleware

- middleware will be executed by the QueueHandler

- phpgram supports Psr 15 Middleware and callable Middleware

- middleware can also be executed from a Psr 11 container

- every middleware will be called with Psr 7 ServerRequestInterface and Psr 15 RequestHandlerInterface and must return a Psr 7 ResponseInterface

- Middleware can add as: 
	- standard middleware (this will be executed on every request)
	- route group middleware (only executed if the route is in the route group)
	- route middleware (only executed if the route is matched)
	
- Mw will be executed from top to bottom and in this order:
	- std middleware
	- group middleware
	- route middleware
	
````php
<?php
use Gram\App\App;

//Standard Middleware

App::app()
	->addMiddleware(new Mw1())						//normal class
	->addMiddleware(Mw2::class)						//class from a psr 11 container
	->addMiddleware(function ($request, $next){		//callable
		return $next($request);
	});

//group
App::app()->group("/prefix",function (){
	//route
	App::app()->get("/function/{id}",function ($id){
    	return "ID = ".$id;
    })
    	->addMiddleware(new RouteMw1())
    	->addMiddleware(RouteMw2::class)
    	->addMiddleware(function ($request, $next){	
    		return $next($request);
    	});
	
})
	->addMiddleware(new GroupMw1())
	->addMiddleware(GroupMw2::class)
	->addMiddleware(function ($request, $next){	
		return $next($request);
	});

//all Middleware will be executed if the route /prefix/function/123 will be matched
````