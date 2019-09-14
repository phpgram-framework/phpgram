# Route

- Eine Route ist die Verbindung zwischen Request und Handler
- eine Route beinhaltet die Url (mit der diese aufgerufen werden soll) und den Handler der geladen werden soll
- siehe [Routing](../technisch/Routing/index.md)
- Route Handler müssen nur den Content für den Response Body zurück liefern alles weitere wird erstellt

## Einfache Route

````php
<?php

use Gram\App\App;
App::app()->add("/user",function (){
	return "User Index";
},['GET']);
````

- Im obrigen Beispiel wird die anonyme Funktion dann ausgeführt wenn der Path der Url ``/user`` lautet
- Die Function ist der Handler und das Array gbit die Http Method (z. B. Get, Post etc) an. Es können auch mehre Methods angegeben werden
- Für den Handler kann alles eingesetzt werden, das der [Callback Creator](../technisch/CallbackCreator/index.md) in ein [Callback](../technisch/Callback/index.md) umformen kann
- der return wäre bei ``/user`` => ``User Index``

## Wildcard Route

- Platzhalter werden mit Regex ermittelt

````php
<?php

use Gram\App\App;
App::app()->add("/user/{id}",function ($id){
	return "Userid = $id";
},['GET']);
````

- In diesem Beispiel wird hinter ``user`` ein Wert erwartet.
- Für den Wert wird alles akzeptiert bis zum nächsten ``/``
- Der [Route Parser](../technisch/Routing/routegeneration.md) extraiert die Parameter und wandelt diese in die Regex: ``[^/]+`` um
- der Parameter wird direkt an die Funktion weiter gegeben
- der return wäre bei ``/user/12aC`` => ``Userid = 12aC``
- auf den Parameter kann auch schon im Request zugegriffen werden (siehe [Middleware](middleware.md))

## Wildcard Route mit Datentypen

````php
<?php

use Gram\App\App;
App::app()->add("/user/{id:\d+}",function ($id){
	return "Userid = $id";
},['GET']);
````
- hier wird mit Regex die Werte die id annehmen kann eingeschränkt
- es kann jede Art von Regex genutzt werden
- der [Route Collector](../technisch/Routing/routeCreation.md) setzt die Klammern um die Regex automatisch nach den ``:``
- ``\d+`` steht für Integer
- bsp.: bei ``/user/12aC`` würde diese Route nicht gematcht werden
- der return bei ``/user/12`` => ``Userid = 12``
- return bei ``/user/12aC`` => ``Not Found``

## Wildcard Route mit Custom Dateitypen

````php
<?php

use Gram\Route\Parser\StdParser;
StdParser::addDataTyp('lang','de|en');
StdParser::addDataTyp('langs','es|ru|fr');
````
- Custom Typen werden direkt im [Route Parser](../technisch/Routing/routegeneration.md) gesetzt
- der Parser wandelt dann den ersten Parameter von ``addDataTyp()`` in die Regex um, die im zweiten Parameter definiert wurde
- hier wird dann ``/{lang}`` in ``(de|en)`` umgewandelt => akzeptiere nur die Wörter de oder en

````php
<?php

use Gram\App\App;

//bsp mit lang

App::app()->add("/page/{l:lang}/{id:n}",function ($l,$id){
	return "Die Sprache ist: $l und die Seite ist: $id";
},['GET']);


//bsp mit langs

App::app()->add("/page/{l:langs}/{id:n}",function ($l,$id){
	return "Die zu übersetzende Sprache ist: $l und die Seite ist: $id";
},['GET']);
````

- ``:n`` ist hier ein Datentyp der bereits von anfang an definiert wurde. Dieser steht für Integer
- Das erste Beispiel gibt bei ``/page/de/21`` => ``Die Sprache ist: de und die Seite ist: 21`` aus
- bei ``/page/ru/21`` => ``Die zu übersetzende Sprache ist: ru und die Seite ist: 21``
- die Typen können dann erweitert werden z. B. mit ``pt`` sollten weitere Sprachen akzeptiert werden ohne die Platzhalter bei jeder einzelnen Route zu ändern

### Standard Typen

- ``n`` => Integer
- ``a`` => Alpha nummerisch
- ``all`` => alles auch der ``/``

## Http Method

- anstatt die Http Method bei jeder Route zu definieren können auch vorgefertigte Methoden genutzt werden
- diese rufen dann auch die add Method mit der jeweiligen Http Method auf

````php
<?php

use Gram\App\App;

//Akzeptiert nur GET als Method
App::app()->get("/user",function (){
	return "User";
});

//Akzeptiert nur POST
App::app()->post("/user",function (){
	return "User";
});

//Akzeptiert GET oder POST
App::app()->getpost("/user",function (){
	return "User";
});

//Akzeptiert PATCH
App::app()->patch("/user",function (){
	return "User";
});

//Akzeptiert DELETE
App::app()->delete("/user",function (){
	return "User";
});

//Akzeptiert PUT
App::app()->put("/user",function (){
	return "User";
});

//Akzeptiert HEAD
App::app()->head("/user",function (){
	return "User";
});
````

## Route Groups

- Gruppen werden immer mit einem Prefix und einer Collector function definiert
- in der function werden die Route eingesammelt
- Das prefix wird dann vor die Url der Route gestellt
- es sind auch nested Groups möglich
- siehe [Route Collector](../technisch/Routing/routeCreation.md)

````php
<?php

use Gram\App\App;

//Admin bereich
App::app()->addGroup("/admin",function (){
	//Route braucht keine Url wenn diese bei /admin ausgeführt werden soll
	App::app()->get("","AdminIndexHandler");
	//Normale Route
	App::app()->get("/role","RoleHandler");
	
	//Nested Groups
	//2. Group /settings
	App::app()->addGroup("/settings",function (){
		//3. Group /dep
		App::app()->addGroup("/dep",function (){
			//Routes
			App::app()->get("","DepIndexHandler");
			
			App::app()->get("/{id}/edit","EditHandler");
		});
	});
});
````

- wenn das Prefix ``/admin`` z. B. zu ``/administrator`` geändert werden würde sind alle Routes in der Gruppe ebenfalls geändert
- um die Department Bearbeiten Funktion auf zurufen muss diese Url aufgerufen werden: ``/admin/settings/dep/{id}/edit``
- Wenn die Route die gleiche Url haben soll wie das Prefix muss bei dieser keine Url angegeben werden

## Psr 7

- der letzte Parameter der bei Functions und Class Methods (nicht bei Controller) ist das Request Objekt
- darauf kann in der Function wie folgt zu gegriffen werden:

````php
<?php
use Gram\App\App;
use Psr\Http\Message\ServerRequestInterface;

App::app()->get("/user/{id}",function ($id, ServerRequestInterface $request){
	return "Userid = $id und die aufgerufene Url ist: ".$request->getUri()->getPath();
});
````

- Output wäre bei ``/user/hallo`` => ``Userid = hallo und die augerufene Url ist: /user/hallo``

## Handler 

- in den Beispielen wurde eine function als Handler benutzt um zu zeigen wie die Parameter zur Function kommen
- es können alle möglichen Muster in den Handler eingesetzt werden, die der [Callback Creator](../technisch/CallbackCreator/index.md) zu einem [Callback](../technisch/Callback/index.md) umformen kann
- Standardgemäß werden vier Arten unterstüzt: functions, Class function, Controller und HandlerInterface
- weitere können, mit eigenen Creators, weitere Muster hinzu gefügt werden

````php
<?php
use Gram\App\App;
use Psr\Http\Message\ServerRequestInterface;

//Class Function:

class ExampleClass
{
	public function exampleMethod($id,ServerRequestInterface $request)
	{
		return "Userid = $id und die aufgerufene Url ist: ".$request->getUri()->getPath();
	}
}

//[Class name, Method name] 
App::app()->get("/user/{id}",["ExampleClass","exampleMethod"]);

//Controller:

use Gram\Middleware\Controller;

class ExampleController extends Controller
{
	public function exampleControllerMethod($id)
	{
		return "Userid = $id und die aufgerufene Url ist: ".$this->request->getUri()->getPath();
	}
}

//Controller@method
App::app()->get("/user/{id}","ExampleController@exampleControllerMethod");
````

- beide Routes geben: bei ``/user/hallo`` => ``Userid = hallo und die augerufene Url ist: /user/hallo`` aus
- Class method funktioniert so wie bei den functions, der request wird auch hier als letzer Parameter übergeben
- Bei Controllern wird nur der Route Parameter übergeben. 
	- siehe [Controller Middleware](../technisch/Middleware/controllermw.md)
	- Das Request Objekt wird in einer Method in der abstrakten Klasse Controller gesetzt
	- Wenn Controller von dieser Klasse erben haben diese Zugriff auf die protected var $request
- Handler können auf den Request zugreifen ihn aber nicht wirksam verändern	