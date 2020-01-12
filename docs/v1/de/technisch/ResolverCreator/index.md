# ResolverCreator

- erstellt aus einem übergebenem Muster ein [Resolver](../Resolver/index.md)

- muss das Interface ``Gram\ResolverCreator\ResolverCreatorInterface`` implementiert haben

````php
<?php
namespace Gram\ResolverCreator;
interface ResolverCreatorInterface
{
	public function createResolver($possibleCallable);

	public function getCallable():ResolverInterface;
}
````

- die Method ``createResolver()`` wird im [ResponseCreator](../Middleware/responsehandle.md) aufgerufen

- sie wendet die Mustererkennung an und erstellt ein [ResolverInterface](../Resolver/index.md)

- mit ``getCallable()`` wird das erstellte Interface Objekt zurück gegeben

- wird im ResponseCreator der [Strategy](../Strategy/index.md) übergeben

## phpgram Standard Creator

der Standard Creator kann folgende Muster erkennen und verarbeiten (siehe dazu auch [Standard Resolver](../Resolver/index.md))

- Function Resolver: ein ``callable`` muss übergeben werden

- Class Resolver: ein Array indem der erste Wert der Klassen- und der zweite der Funktionsname ist

- Handler Resolver: ein Objekt das das Interface ``Gram\Middleware\Handler\HandlerInterface`` implementiert hat

Wenn ein Muster passt wird das entsprechende Resolver mit den Resolver Klassen erstellt (siehe [Resolver](../Resolver/index.md))


## Anpassung

- Vor dem Start der App kann in der [App](../App/index.md) Klasse ein Standardcreator gesetzt werden.

- Wenn keiner gesetzt wurde wird der phpgram Standard Creator verwendet

- [Middleware](../Middleware/index.md) haben die Möglichkeit durch manipulation des requests einen anderen Creator zu setzen

- Sollte keiner gesetzt sein wird der in App definierte Creator ausgeführt

- Der neue Creator kann z. B. von dem Standard erben um die Standard Muster ebenfalls ab zudecken