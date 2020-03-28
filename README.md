[![](https://gitlab.com/grammm/php-gram/phpgram/raw/master/docs/img/Feather_writing.svg.png)](https://gitlab.com/grammm/php-gram/phpgram)

# phpgram

[![pipeline status](https://gitlab.com/grammm/php-gram/phpgram/badges/master/pipeline.svg)](https://gitlab.com/grammm/php-gram/phpgram/commits/master)
[![Packagist Version](https://img.shields.io/packagist/v/phpgram/phpgram)](https://packagist.org/packages/phpgram/phpgram)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/phpgram/phpgram)](https://gitlab.com/grammm/php-gram/phpgram/blob/master/composer.json)
[![Packagist](https://img.shields.io/packagist/l/phpgram/phpgram)](https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE)

A very fast and lightweight Php Framework for small to enterprise applications.

- RouteHandler for Functions and Classes

- Request and Response via [Psr-7](https://www.php-fig.org/psr/psr-7/) 

- Middleware via [Psr-15](https://www.php-fig.org/psr/psr-15/) 

- [Psr 11](https://www.php-fig.org/psr/psr-11/) Container Support for Automatic Dependency Injection (Autowiring) for Route Callable: Class (in constructor) or Function (with ``__get()``)

- Response Creation (via [Psr-17](https://www.php-fig.org/psr/psr-17/) Response Factory) and Strategies

- **Supports Async Requests**

## Install

Via Composer

``` bash
composer require phpgram/phpgram
```

## [Documentation](https://grammm.gitlab.io/php-gram/phpgram/)

````php
<?php

use Gram\App\App;

use Gram\Strategy\StdAppStrategy;

App::app()->debugMode(0);	//0 = Render Exceptions, 1 = only show headline, 2 = show nothing

App::app()->setStrategy(new StdAppStrategy()); //define how the Output will be created

App::app()->set404("Er404@404"); //404 Handler
App::app()->set405("Er405@405"); //405 Handler

//Middleware will be executed before the Router
App::app()
	->addMiddleware(new Session())
	->addMiddleware(new Authenticate());


//Routes and Groups

App::app()->get("/","Index@index"); 	//static Route

//dynamic Route with Middleware (this Middleware will only executed if the Routes is matched
App::app()->get("/video/{id:n}","Video@watch")->addMiddleware(new Caching);

//Nested Group with Middleware
// /admin will be the prefix for all Routes in this Group
App::app()->group("/admin",function (){
	
	App::app()->get("","AdminIndexHandler");
	
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

//Group with different Output Strategy (json)
use Gram\Strategy\JsonStrategy;
App::app()->group("/",function (){
	App::app()->getpost("video/vote/{id:n}","Video@vote");
	App::app()->getpost("video/getComment/{id:n}","Video@getComments");
})->addStrategy(new JsonStrategy());


//Psr 7 Request

// in this case with Nyholm Psr 7

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

$psr17Factory = new Psr17Factory();

$creator = new ServerRequestCreator($psr17Factory,$psr17Factory,$psr17Factory,$psr17Factory);
$request = $creator->fromGlobals();

//App needs a Psr 17 Response Factory
App::app()->setFactory($psr17Factory);

//let the App emit the Response
App::app()->start($request);
````

## License

phpgram is open source and under [MIT License](https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE)

## Credits
### Router
- Algorithm and Core Implementation: Copyright by Nikita Popov. ([FastRoute](https://github.com/nikic/FastRoute))
- Parser: Copyright by Nikita Popov and Phil Bennett ([thephpleague](https://github.com/thephpleague/route))

### Emitter
- Based on [zend-httphandlerrunner](https://github.com/zendframework/zend-httphandlerrunner). Copyright [Zend Technologies USA, Inc. All rights reserved](https://github.com/zendframework/zend-httphandlerrunner/blob/master/LICENSE.md)
