# Class Middleware

````php
<?php

namespace Gram\Middleware\Classes;

interface ClassInterface
{
	
}
````

- Ein Interface, dass alle Klassen implementiert haben müssen. Sonst wird eine Exception geworfen

- Es ist dazu da dem Object der Klasse die Psr Objects: Request, Response und Container zu kommen zulassen

- Das Interface enthält nur Getter und Setter Methods

- Der [ResponseCreator](responsehandle.md) übergibt die Psr Objecte an den jeweiligen [Resolver](../Resolver/index.md), der die Objects wiederum durch dieses Interface dem Object der auf zurufenden Klasse zukommen lässt

- Nachdem Durchlauf nimmt der ResponseCreator den Response wieder entgegen

- Klassen können wie Functions auf den Psr 11 Container zugreifen sowie mit ``$this->value`` auf einen index im Container
mit der ``__get()`` Function

## Vorgefertigte Implementierung

````php
<?php

namespace Gram\Middleware\Classes;

trait ClassTrait
{
	
}
````  

- dieses Trait kann in Klassen genutzt werden mit ``use ClassTrait;`` um die Methods zu implementieren 

<br>

[hier geht es zurück zum Start](index.md)

### Inhalt Middleware
[1. Start](index.md) <br>
[2. QueueHandler](queuehandle.md) <br>
[3. ResponseCreator](responsehandle.md) <br>
[4. Routing Middleware](routingmw.md) <br>
[5. Middleware Collector](mwcollector.md) <br>
[6. Class Middleware](classmw.md)