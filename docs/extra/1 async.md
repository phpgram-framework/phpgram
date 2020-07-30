# Asynchronous Requests and long running php

You can easily create async non blocking requests or long running php with phpgram. 

1. Setup your App (e.g. routes, [Psr 11 Container](), [Psr 17 ResponseFactory]())
2. Build the services e.g. ResponseCreator, Middleware, Routes (with `build()` function)
3. use the `handle()` function from [Psr 15]() RequestHandlerInterface. It is very important to use a [Psr 7]() ServerRequest. 
If its not available try to create one with the factory

A new resolver will be created on every request. So you can use class variables.

Maybe you need to call `gc_collect_cycles()` after 1000 requests but phpgram itself doesn't leak memory.


````php
<?php

//1. App Setup

use Gram\App\App;
use Nyholm\Psr7\Factory\Psr17Factory;

//Routes

App::app()->get("/",function (){
	return "Hello World";
});

App::app()->group("/user",function (){
	App::app()->get("/{id:n}",UserController::class."@get");
	App::app()->post("",UserController::class."@create");
});

$psr17 = new Psr17Factory();

//Response Factory e.g. Nyholm
App::app()->setFactory($psr17);

App::app()->debugMode(true);

//DI, e.g. Pimple

$pimple = new \Pimple\Container();

$pimple[UserModel::class] = function () {
	return new UserModel();	
};

App::app()->setContainer(new \Pimple\Psr11\Container($pimple));

//2. Build the services
App::app()->build();

//3. use handle()

//Your async provider bootstrap

$server = function(\Psr\Http\Message\ServerRequestInterface $request) {
	return App::app()->handle($request);	
};

//This server function needs to be called on every request.

````
 