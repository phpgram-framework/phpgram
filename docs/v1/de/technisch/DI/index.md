# DI

- Phpgram besitzt die Möglichkeit automatisch Dependencies aus einem Psr 11 Container in den Constructor der 
auf zurufende Klasse zu parsen.

- Dank Psr 11 kann dazu jeder Container der den Standard implementiert hat benutzt werden

- der Container kann so gesetzt werden: 
````php
<?php
use Gram\App\App;

$container = new Psr11Container();

//Build up the Container

App::app()->setContainer($container);
````

## Psr 11

- Der Standard schreibt zwei Methods vor:
	- get
	
	- has

- Mit has() kann abgefragt werden ob es einen übergebenen Eintrag gibt

- Mit get wird dieser Eintrag aus dem Container zurück gegeben

- Mit der Auto DI wird nur ein index gesucht und gefunden wenn dieser mit dem vollen Klassennamen (mit Namespace) 
oder nur der Klassenname drin ist

## Funktionsweise im Standard [ClassResolver](../Resolver/index.md) (Auto DI)

1. Prüfe zuerst ob die Klasse ausführbar ist und ob sie das benötigte Interface implementiert hat (siehe [Class Middleware](../Middleware/classmw.md))

2. Erstelle aus der Class ein neues Object ggf. mit den Constructor Dependencies

3. Dazu untersuche den Constructor mithilfe von ReflectionClass

4. Prüfe zuerst ob es einen Constructor gibt und ob ein Container vorliegt. Ist eins von beiden nicht der Fall wird einfach ein neues Object der Klasse erstellt

5. Sonst werden die Parameter die der Constructor bekommt geprüft

6. Durchsuche alle Klassennamen der Parameter ob diese im übergebenem Container sind

7. Wenn nicht wird geprüft ob der Parameter einen Default Wert hat und dann wird dieser geladen. Wenn nicht gebe Exception aus

8. Wenn es diesen Namen (mit oder ohne Namespace) im Container gibt wird der Wert aus dem Container geladen

9. Das Object wird dann, mithilfe von ReflectionClass, mit den Parameter aus dem Container erstellt

10. Danach werden noch die Psr Objects mit einem Interface gesetzt und die Method wird mit call_user_function_array gestartet (Die Parameter kommen vom [Router](../Middleware/routingmw.md))


## Funktionsweise im Standard [FunctionResolver](../Resolver/index.md)

1. Binde das Closure (die anonymous function) an den Resolver

2. Somit hat die Function Zugriff mit ``$this->value`` auf alle Public Variables der gebundenen Klasse

3. Die vom ResponseCreator übergebenen Objects werden als Public gespeichert
somit hat die Function darauf zugriff


## Cheating DI (Anti-Pattern!)

- Es kann auch einen Index mit ``$this->value`` in Klassen zugegriffen werden, da diese auch eine ``__get()`` Method implementiert haben
die den Wert aus dem Container läd, wie bei den Functions

- Dies sollte in Klassen aber nicht genutzt werden, da bei der DI es immer klar ersichtlich sein muss
welche Klasse bzw. Dependency inject wird.

- Somit ist es besser die Dependencies bereits im Constructor fest zulegen und sie dann mithilfe der Auto DI übergeben bekommt
