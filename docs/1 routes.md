# Routing

Routes decide which handle is called up for which URL

It's completely separated from the core. 
The route component can also be used standalone without Psr 7

````php
<?php

use Gram\App\App;

//A GET route is only matched for GET requests
App::app()->get("url_pattern","route_handle");
````

## Route Handle

A handle is called when the url pattern matched.

### Route Handle Types

In addition to the standard types, more can be defined see [Response creation](3%20responsecreation.md)

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

## Route Pattern

The router uses regex to match the requested url with the defined routes.

### Pattern types

- static: 
	- The url must be exactly the same as the route pattern
	- e.g. `/admin/index`
	
<br>

- wildcard:
	- The url can have parameters
	- e.g. `/user/{id}`
	- this pattern will be matched by `/user/username`, `/user/123` ...
	
	
#### Wildcard Routes

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

## Route groups

Groups are a very pleasant way define routes with the same prefix

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

## Http Methods

- this routes will only matched if the http method is the same

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

## [Middleware](2%20mw.md) and [Strategies](5%20strategy.md)

Routes and route groups can have its own Middleware and Strategies

````php
<?php
use Gram\App\App;

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

The order for middleware is: 1st group, 2nd route

and for strategies: 1st route, only if there is no route strategy -> group