# Callback

- wird im [ResponseHandler](../Middleware/responsehandle.md), vom [CallbackCreator](../CallbackCreator/index.md), anhand einer Mustererkennung, erstellt

- muss das Interface ``Gram\Callback\CallbackInterface`` implementiert haben

- wird in der [Strategy](../Strategy/index.md) ausgeführt

- wird als letzte Middleware aufgerufen

## CallbackInterface

````php
<?php
namespace Gram\Callback;
interface CallbackInterface
{
	public function callback($param=[],ServerRequestInterface $request);

	public function set();
}
````


- mit ``set()`` wird das Callback gesetzt. Diese Funktion kann unterschiedliche Parameter erwarten für die utnerschiedlichen Callbacks deswegen wurde im Interface kein Parameter vor gegeben

- mit ``callback()`` wird das in ``set()`` gesetze Callback mit Parametern und dem request gestartet

- Callbacks können zwar aauf den Request zugreifen, sie können diesen aber nicht wirksam verändern

- je nach [Strategy](../Strategy/index.md) wird anders mit dem return des Callbacks umgegangen

## phpgram Standard Callbacks

Als Standard gibt es:

- Function Callback 

	- hier wird eine anonyme Function (Closure) ausgeführt

	- die Function wird an die Callback Klasse gebunden und kann somit mit ``$this->request`` auf den Request zugrifen

- Class Callback 

	- hier wird aus einem übergebenen Klassen- und Funktionsnamen ein Callback erstellt

	- der request wird hier auch als letzter Parameter hinzu gefügt
	
	- Wenn die Class das Interface ``Gram\Middleware\Classes\ClassInterface`` implementiert hat kann diese den request wirkungsvoll verändern (siehe [Class Middleware](../Middleware/classmw.md))

- Controller Callback

	- erbt von Class Callback

	- bekommt Klassen und Funktionsnamen mit einem @ daziwschen übergeben: ``"class@function"``

	- trennt bei dem @ Zeichen Klasse und Funktion und setzt diese im Class Callback wieder ein

	- bei ``callback()`` wird der Request nicht als Funktionsparamter hinzugefügt sondern an eine extra Method übergeben (siehe [Class Middleware](../Middleware/classmw.md))

- Handler Callback

	- erwartet ein ``HandlerInterface`` Objekt

	- führt bei ``callback`` die Method ``handle()`` aus

	- wird für spezielle Middleware Handler verwendet (siehe [CallbackCreator](../CallbackCreator/index.md))
	

## Anpassung

- sollten unter den Standard Callbacks keine passenden zu finden sein kann ein eigener CallbackCreator, der eigene Muster erkennt, und Callbacks erstellt werden

- Bedingung ist, dass sie die Interfaces implementiert haben