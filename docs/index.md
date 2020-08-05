# Phpgram

[![](https://gitlab.com/grammm/php-gram/phpgram/raw/master/docs/img/Feather_writing.svg.png)](https://gitlab.com/grammm/php-gram/phpgram)

<br>

Phpgram is just a http request router with a middleware dispatcher.

## Getting Started

### Hello World

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

//get the response with the content
$response = App::app()->start($request);

//simple emit:
echo $response->getBody(); 	// don't do this in production use an psr 7 emitter!
````

- if `/` is matched its print out "Hello World"

- To start the App needs a Psr 17 Response Factory and a Psr 7 Request Object

- in this case with Nyholm Psr 7


## Workflow

### App Start

The App class mange all configurations

The App class uses a singleton like mechanism: `App::app` <br>
This static function always returns the same App instance. 

Before start:

- define [middleware](2%20mw.md) if need

- define [routes](1%20routes.md)

- set a [Psr 17](https://www.php-fig.org/psr/psr-17/) response factory

- create a [Psr 7](https://www.php-fig.org/psr/psr-7/) ServerRequest


After calling the function `start()` a Psr 7 Response will be returned. This response can emit with every prs 7 emitter.

To configure the App see more information [here](0%20app.md)

### How the framework works

The App class is a [Psr 15](https://www.php-fig.org/psr/psr-15/) RequestHandlerInterface. 

#### App lifecycle

1. configure the app (incl. routing and middleware)
2. start with a Psr 7 ServerRequest
3. build the [QueueHandler](2%20mw.md#queuehandler), [response creator](3%20responsecreation.md) and [route middleware](2%20mw.md#route-middleware)
4. start the queue handler with `handle()` 
5. call the standard middleware and after the route middleware (incl the [routing](1%20routes.md))
6. call the new mw from the route
7. call the [route handle](1%20routes.md#route-handle) (e.g. a class) with the [strategy](5%20strategy.md) in the response creator
8. send back the response
