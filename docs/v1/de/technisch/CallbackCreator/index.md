# CallbackCreator

- erstellt aus einem übergebenem Muster ein [Callback](../Callback/index.md)

- muss das Interface ``Gram\CallbackCreator\CallbackCreatorInterface`` implementiert haben

````php
<?php
namespace Gram\CallbackCreator;
interface CallbackCreatorInterface
{
	public function createCallback($possibleCallable);

	public function getCallable():CallbackInterface;
}
````

- die Method ``createCallback()`` wird im [ResponseCreator](../Middleware/responsehandle.md) aufgerufen

- sie wendet die Mustererkennung an und erstellt ein [CallbackInterface](../Callback/index.md)

- mit ``getCallable()`` wird das erstellte Interface Objekt zurück gegeben

- wird im ResponseCreator der [Strategy](../Strategy/index.md) übergeben

## phpgram Standard Creator

der Standard Creator kann folgende Muster erkennen und verarbeiten (siehe dazu auch [Standard Callback](../Callback/index.md))

- Function Callback: ein ``callable`` muss übergeben werden

- Class Callback: ein Array indem der erste Wert der Klassen- und der zweite der Funktionsname ist

- Controller Callback: ein zusammengesetzer String (Controller@Function)

- Handler Callback: ein Objekt das das Interface ``Gram\Middleware\Handler\HandlerInterface`` implementiert hat

Wenn ein Muster passt wird das entsprechende Callback mit den Callback Klassen erstellt (siehe [Callback](../Callback/index.md))


## Anpassung

- Vor dem Start der App kann in der [App](../App/index.md) Klasse ein Standardcreator gesetzt werden.

- Wenn keiner gesetzt wurde wird der phpgram Standard Creator verwendet

- [Middleware](../Middleware/index.md) haben die Möglichkeit durch manipulation des requests einen anderen Creator zu setzen

- Sollte keiner gesetzt sein wird der in App definierte Creator ausgeführt

- Der neue Creator kann z. B. von dem Standard erben um die Standard Muster ebenfalls ab zudecken