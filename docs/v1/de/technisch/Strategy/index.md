# Strategy

- entscheidet wie der Output des callable im [ResponseCreator](../Middleware/responsehandle.md) zu erstellen ist

- es muss immer einen return geben der als Content in dem Response gespeichert wird

- Strategies können als Standard in der [App](../App/index.md) Klasse gesetzt oder bei einer Routegroup bzw. Route gesetzt werden

- Es wird immer nur die zuletzt gesetze Strategy ausgeführt

- Sollte in der App keine Standard Strategy gesetzt worden sein wird die phpgram StdStrategy genutzt

- Strategies müssen das Interface ``Gram\Strategy\StrategyInterface`` implementiert haben

````php
<?php
namespace Gram\Strategy;
interface StrategyInterface
{
	public function getHeader();

	public function invoke(CallbackInterface $callback, array $param, ServerRequestInterface $request);
}
````
- mit ``getHeader()`` kann der Content-Typ Header gesetzt werden

- mit ``invoke()`` wird das [Callback](../Callback/index.md) ausgeführt mit den Route Paramtern und dem Request Objekt

- der Request kann hier nicht mehr geändert werden 

## phpgram Standard Strategies

- StdAppStrategy: diese führt das Callback aus und gibt das Return des Callbacks zurück

- BufferAppStrategy: erbt von StdAppStrategy öffnet den Output Buffer bevor das Callback ausgeführt wird und gibt den Inhalt des Buffers zurück

- JsonStrategy: wandelt das return des Callbacks in ein json Dateiformat um