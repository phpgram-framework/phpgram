# Resolver

- wird im [ResponseCreator](../Middleware/responsehandle.md), vom [ResolverCreator](../ResolverCreator/index.md), anhand einer Mustererkennung, erstellt

- muss das Interface ``Gram\Resolver\ResolverInterface`` implementiert haben

- wird in der [Strategy](../Strategy/index.md) ausgeführt

- wird als letzte Middleware aufgerufen

## ResolverInterface

````php
<?php
namespace Gram\Resolver;
interface ResolverInterface
{
	public function resolve($param=[]);

	public function set();
}
````


- mit ``set()`` wird das Resolver gesetzt. Diese Funktion kann unterschiedliche Parameter erwarten für die utnerschiedlichen Resolvers deswegen wurde im Interface kein Parameter vor gegeben

- mit ``resolve()`` wird das in ``set()`` gesetze Resolver mit Parametern und dem request gestartet

- Resolvers können zwar aauf den Request zugreifen, sie können diesen aber nicht wirksam verändern

- je nach [Strategy](../Strategy/index.md) wird anders mit dem return des Resolvers umgegangen

## phpgram Standard Resolvers

Als Standard gibt es:

- Function Resolver 

	- hier wird eine anonyme Function (Closure) ausgeführt

	- die Function wird an die Resolver Klasse gebunden und kann somit mit ``$this->request`` auf den Request zugrifen

- Class Resolver

	- bekommt Klassen und Funktionsnamen mit einem @ daziwschen übergeben: ``"class@function"``

	- trennt bei dem @ Zeichen Klasse und Funktion und setzt diese im Class Resolver wieder ein

	- (siehe [Class Middleware](../Middleware/classmw.md))
	
	- [Dependency Injection](../DI/index.md) ist bei Klassen auch möglich

- Handler Resolver

	- erwartet ein ``HandlerInterface`` Objekt

	- führt bei ``Resolver`` die Method ``handle()`` aus

	- wird für spezielle Middleware Handler verwendet (siehe [ResolverCreator](../ResolverCreator/index.md))
	
- Callable Resolver

	- führt ein Callable (Klassen mit der Method __invoke() functions) aus
	
	- das Callable muss Request, Response, und die Routeparameter annehmen
	
	- Wenn Response verändert werden soll muss der Response zurück gegeben werden

## Anpassung

- sollten unter den Standard Resolvers keine passenden zu finden sein kann ein eigener ResolverCreator, der eigene Muster erkennt, und Resolvers erstellt werden

- Bedingung ist, dass sie die Interfaces implementiert haben