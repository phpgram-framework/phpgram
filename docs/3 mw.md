# Middleware

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