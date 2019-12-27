# User Guide für Phpgram

## Inhalt

[App Einstellungen](appoptions.md) <br>
[Routes definieren](route.md) <br>
[Middleware erstellen und einsetzen](middleware.md) <br>
[Request manipulieren](requestmanipulation.md) <br>
[Strategy erstellen und einsetzen](strategy.md) <br>
[Response](response.md) <br>

## Anwendungsbeispiel

````php
<?php

use Gram\App\App;

use Gram\Strategy\StdAppStrategy;

//debug mode setzen: 0 = Exception werfen, 1 = Nur Angeben, dass es einen Error gab, 2 = Exception ausgeben

App::app()->debugMode(0);	//0 ist voreingestellt

App::app()->setStrategy(new StdAppStrategy()); //ggf. Strategy verändern. StdAppStrategy ist immer als Standard gesetzt wenn nichts überschrieben wurde

App::app()->set404("Er404@404"); //404 Handler setzen
App::app()->set405("Er405@405"); //405 Handler setzen

//Standard Mw setzen die Immer ausgeführt werden soll
App::app()
	->addMiddle(new Session())
	->addMiddle(new Authenticate());


//Route und Gruppen setzen

App::app()->get("/","Index@index"); 	//Normale Route

//Route mit Middleware
App::app()->get("/video/{id:n}","Video@watch")->addMiddleware(new Caching);

//Nested Group mit Middleware

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

//Gruppe mit einer anderen Strategy z. B. für json
use Gram\Strategy\JsonStrategy;
App::app()->group("/",function (){
	App::app()->getpost("video/vote/{id:n}","Video@vote");
	App::app()->getpost("video/getComment/{id:n}","Video@getComments");
})->addStrategy(new JsonStrategy());

//App starten

//Request erstellen

//hier am Beispiel mit Nyholm

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

$psr17Factory = new Psr17Factory();

$creator = new ServerRequestCreator($psr17Factory,$psr17Factory,$psr17Factory,$psr17Factory);
$request = $creator->fromGlobals();

//Der App die Psr 17 Factories übergeben
App::app()->setFactory($psr17Factory,$psr17Factory);

//der App den request übergeben und starten
App::app()->start($request);
````