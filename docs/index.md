# Phpgram Doc

[![](https://gitlab.com/grammm/php-gram/phpgram/raw/master/docs/img/Feather_writing.svg.png)](https://gitlab.com/grammm/php-gram/phpgram)


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
