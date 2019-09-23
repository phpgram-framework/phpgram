# Class Middleware

````php
<?php

namespace Gram\Middleware\Classes;

interface ClassInterface
{
	/**
	 * Setze Psr Object(s) in der Klasse
	 *
	 * @param ServerRequestInterface $request
	 * @return mixed
	 */
	public function setPsr(ServerRequestInterface $request);

	/**
	 * Gebe Psr Object(s) wieder zurück
	 *
	 * @return ServerRequestInterface
	 */
	public function getRequest():ServerRequestInterface;
}
````

- Ein Interface, dass alle Klassen implementieren können wenn diese den Request wirkungsvoll verändern wollen

- und das alle Controller implementieren müssen

- Der Request wird vom [ResponseCreator](responsehandle.md) an die [Strategy](../Strategy/index.md) übergeben und diese wiederung ruft das [Callback](../Callback/index.md) auf mit diesem Request

- Das Class und ControllerCallback setzt dann den Request ein

- Nach dem die Klasse fertig ist holt sich der ResponseCreator den Request wieder zurück

## Vorgefertigte Implementierung

````php
<?php

namespace Gram\Middleware\Classes;

trait ControllerTrait
{
	/** @var ServerRequestInterface */
	protected $request;

	/**
	 * @inheritdoc
	 */
	public function setPsr(ServerRequestInterface $request)
	{
		$this->request=$request;
	}

	/**
	 * @inheritdoc
	 */
	public function getRequest():ServerRequestInterface
	{
		return $this->request;
	}
}
````  

- dieses Trait kann in Klassen genutzt werden mit ``use ControllerTrait;`` um die Methods zu implementieren 

<br>

[hier geht es zurück zum Start](index.md)

### Inhalt Middleware
[1. Start](index.md) <br>
[2. QueueHandler](queuehandle.md) <br>
[3. ResponseCreator](responsehandle.md) <br>
[4. Routing Middleware](routingmw.md) <br>
[5. Middleware Collector](mwcollector.md) <br>
[6. Class Middleware](classmw.md)