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

5. 