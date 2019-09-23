# Phpgram DE Doc

# Program Life Cycle

## Vorbereitung

1. Routes definieren: ``App::app()->add()`` (siehe [Route](../Use/route.md))

2. ggf. Basepath für die Urls definieren: ``App::app()->setBase()``

3. 404 und 405 Seiten für den Router setzen: ``App::app()->set404()`` und ``App::app()->set404``

4. ggf. Standard [Middleware](Middleware/index.md) definieren: ``App::app()->addMiddle()``

5. ggf. Standard [Strategy](Strategy/index.md) definieren: ``App::app()->setStrategy()``, diese muss in StrategyInterface übergeben bekommen

6. ggf. [Callback Creator](CallbackCreator/index.md) definieren: ``App::app()->setCallbackCreator()``, hier wird ein CallbackCreatorInterface erwartet

7. Zuletzt die Psr 17 Response und Stream Factories setzen: ``App::app()->setFactory()``, hier werden ResponseFactoryInterface und StreamFactoryInterface erwartet

## Start der App

1. Starten mit ``App::app->start($request)``, hier muss ein vorgefertigtes ServerRequestInterface übergeben werden

2. erstelle Standard Strategy und Creator (siehe Vorbereitung 5. und 6.)

3. erstelle den [ResponseCreator](Middleware/responsehandle.md)

4. erstelle den [QueueHandler](Middleware/queuehandle.md)

5. erstelle die [Routing Middleware](Middleware/routingmw.md)

6. erstelle mithilfe der Routing Middleware den Middleware Stack im QueueHandler

7. Füge die Routing Mw dem Stack als letztes Element hinzu

8. führe den QueueHandler aus

9. der QueueHandler läuft durch alle Standard Middleware

10. Sollte kein Fehler oder Event getriggert worden sein wird das [Routing](Routing/index.md) ausgeführt in der Routing Middleware

11. ggf. werden die für die Route definierten Mw ausgeführt

12. Der Response wird durch den ResponseCreator erstellt und zurück gegeben

13. Der Response wird dem [Emitter](App/emit.md) übergeben der den Header und den Body ausgibt


## Ablauf Kurzfassung

[App](App/index.md) -> [QueueHandler](Middleware/queuehandle.md) -> Std Middleware -> [Routing Mw](Middleware/routingmw.md) -> weitere Mw -> [ResponseCreator](Middleware/responsehandle.md) -> [Strategy](Strategy/index.md) -> ResponseCreator -> App -> [Emitter](App/emit.md)

<br>

### Inhalt Doc
[App](App/index.md) <br>
[Middleware](Middleware/index.md) <br>
[Routing](Routing/index.md) <br>
[Callback Creator](CallbackCreator/index.md) <br>
[Callback](Callback/index.md) <br>
[Strategy](Strategy/index.md) <br>
[Emitter](App/emit.md)