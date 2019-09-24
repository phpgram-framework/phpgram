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

## Funktionsweise im Standard [ClassResolver](../Resolver/index.md)

1. Prüfe zuerst ob die Klasse ausführbar ist und ob sie das benötigte Interface implementiert hat (siehe [Class Middleware](../Middleware/classmw.md))

2. Erstelle aus der Class ein neues Object gg.f mit den Constructor Dependencies

3. Dazu untersuche den Constructor mithilfe von ReflectionClass

4. Prüfe zuerst ob es einen Constructor gibt und ob ein Container vorliegt. Ist eins von beiden nicht der Fall wird einfach ein neues Object der Klasse erstellt

5. Sonst werden die Parameter die der Constructor bekommt geprüft

6. Durchsuche alle Klassennamen der Parameter ob diese im übergebenem Container sind

7. Wenn nicht wird geprüft ob der Parameter einen Default Wert hat und dann wird dieser geladen. Wenn nicht gebe Exception aus

8. Wenn es diesen Namen (mit oder ohne Namespace) im Container gibt wird der Wert aus dem Container geladen

9. Das Object wird dann, mithilfe von ReflectionClass, mit den Parameter aus dem Container erstellt

10. Danach werden noch die Psr Objects mit einem Interface gesetzt und die Method wird mit call_user_function_array gestartet (Die Parameter kommen vom [Router](../Middleware/routingmw.md))