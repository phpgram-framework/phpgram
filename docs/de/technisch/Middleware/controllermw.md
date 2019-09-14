# Controller Middleware

````php
<?php
namespace Gram\Middleware;
abstract class Controller
{
	public function setPsr(ServerRequestInterface $request)
	{
		
	}
}
````
- Controller bzw. Klassen die in den Routes so definiert wurden ``"Controller@Function"`` können von dieser Klasse erben
- Beim Aufruf im [ResponseHandler](responsehandle.md) wird im [Callback](../Callback/index.md) die Method ``setPsr(ServerRequestInterface $request)`` aufgerufen
- somit erhalten diese Klassen zugriff auf den Request ohne, dass dieser der aufzurufenden Method übergeben werden muss


<br>

[hier geht es zurück zum Start](index.md)

### Inhalt Middleware
[1. Start](index.md) <br>
[2. QueueHandler](queuehandle.md) <br>
[3. ResponseHandler](responsehandle.md) <br>
[4. Routing Middleware](routingmw.md) <br>
[5. Middleware Collector](mwcollector.md) <br>
[6. Controller Middleware](controllermw.md)