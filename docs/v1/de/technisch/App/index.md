# App 

- die Programmsteuerung befindet sich in der App Klasse

````php
<?php
namespace Gram\App;
class App
{
	
}
````
- für die Steuerung siehe [Startseite](../index.md)

- nur in der App Klasse werden Objekte definiert

- die dann die anderen Klassen als Interface Objekte übergeben bekommen

- App dient auch als Route Collector Factory somit wird sicher gestellt, dass alle Routes in einem Route Collector Objekt erfasst werden

- Definiert:

	- [Router](../Routing/router.md)

	- [Route Collector](../Routing/routeCreation.md)

	- [Middleware Collector](../Middleware/mwcollector.md)

	- [Strategy Collector](../Strategy/index.md)

	- Psr 17 Factories
	
	- [Standard Strategy](../Strategy/index.md)

	- [Standard Callback Creator](../CallbackCreator/index.md)

	- [ResponseHandler](../Middleware/responsehandle.md)

	- [QueueHandler](../Middleware/queuehandle.md)

	- [Routing Middleware](../Middleware/routingmw.md)

	- [Emitter](emit.md)

- Führt alle Route add Methods vom Route Collector aus