# Strategy

- siehe Technische Doc [Strategy](../technisch/Strategy/index.md)

- Strategies bestimmten wie der Output des [Route Handlers](route.md) erfasst werden soll

- Es muss immer ein Output generiert werden für den [Response](response.md)

- Der Output darf erst im Emitter (siehe [Response](response.md) und [Emitter](../technisch/App/emit.md)) zum Client gesendet werden

## Strategies erstellen

- Strategies müssen das Interface ``Gram\Strategy\StrategyInterface`` implementiert haben

- siehe [Strategy](../technisch/Strategy/index.md)

## Strategies setzen

- Es wird unterschieden zwischen Standard Strategy (die immer ausgeführt wird sollte keine andere definiert worden sein)

- und Route und Route Group Strategy

### Std Stragey setzen

- ``App::app()->setStrategy()``

- Hier wird ein StrategyInterface erwartet

- wenn keine gesetzt wird wird die ``Gram\Strategy\StdAppStrategy`` ausgeführt

### Routegroup Strategy setzen

- zu jeder [Routegroup](route.md) kann eine Strategy gesetzt werden

````php
<?php
use Gram\App\App;
use Gram\Strategy\StdAppStrategy;
use Gram\Strategy\BufferAppStrategy;

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
	})->addStrategy(new BufferAppStrategy());
	
})->addStrategy(new StdAppStrategy());
````
- hier wurden zwei Strategies gesetzt

- es wird immer die letzte Strategy ausgeführt

- in diesem Beispiel:

	- für ``/admin`` und ``/admin/role`` die StdAppStrategy

	- für ``/admin/stettings/dep...`` die BufferAppStrategy
	
### Route Strategy

````php
<?php
use Gram\App\App;
use Gram\Strategy\BufferAppStrategy;
App::app()->get("/user/{id}","ExampleController@exampleControllerMethod")->addStrategy(new BufferAppStrategy());
````

- wenn die Route gematcht wird, wird die BufferAppStrategy ausgeführt auch wenn in der Routegroup eine andere gesetzt wurde
