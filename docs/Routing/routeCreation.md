# Route Creation
- Routes werden mit Collectors gesammelt und mit Generatoren zusammengefasst
- Es gibt zwei Arten von Routes:
	- Static
	- dynamic
1. Wenn du Routes zur Map zusammengefasst werden sollen rufe zuerst den Collector auf
2. Dieser nimmt die defienierten Routes (aus dem Ordner config/routes) entgegen und sammelt sie
3. Wenn die Route Placeholder hat -> wandle diese um und speichere die Anzahl, mit der Route und ihrem Handler, bei den Dynamischen Routes
4. Wenn nicht bei den Static
5. In den Arrays ist jeweils die Route und der Handler für diese Route zusammen drin
6. Rufe zum Schluss die Generatoren für Static und dynamic auf
## Static Creation
- Eine Static Route ist eine Route ohne Placeholder z. B. /video/add
## Static Generator
- Klasse: 
```php
<?php 
namespace Gram\Route\Generator;
class StaticGenerator
{
	
}
```
1. Laufe durch alle gesammelten Routes und 
2. gebe die Routes und Handler in getrennten Arrays zurück
## Dynamic Creation
- Eine dynamische Route ist eine mit Placeholdern z. B. /video/{id}/watch
- Problem: jede Route einzeln in einer Schleife ab zulaufen und per regex zu vergleichen kostet sehr viel Zeit
- Lösung: Fasse die Routes in eine gemeinsame Regex zusammen: [Quelle: nikic Fastroute](http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html)
- Weiteres Problem: Bei vielen Routes wird die Regex zu groß um sie noch verhältnismäßig zu prüfen
- Lösung: die Regex chunken in 10er Gruppen. Somit müssen nur noch (Anzahl Routes)/10 preg_matches durchgeführt werden
- Hier wird die Methode: Group Count Based (GCB) verwendet
### GCB
- Hier werden 10 Regex zu einer zusammengefasst
- alle Chunks werden als Array abgespeichert
- dieses wird dann vom Dispatcher durchlaufen
- Um zu wissen welche Regex gefunden wurde wird die Anzahl der Matches als Index betrachtet
- Dazu muss die Regex auch genügend Placeholder besitzen
- Tut sie dies nicht werden die Fehlenden Placeholder mit Dummy Placeholdern: () aufgefüllt
- bsp.: eine Route die an 5. Stelle steht mit einem Placeholder sieht dann so aus: `/video/(.*)()()()()`
	- Wenn /video/(.*) gematcht wird hat die Regex 5 Matches
	- Somit kann der Handler an 5. Stelle in der Handlerliste zurück gegeben werden (siehe Dynamic Dispatcher)
## Dynamic Generator
- Klasse: 
```php
<?php 
namespace Gram\Route\Generator;
class DynamicGenerator
{
	
}
```
1. Laufe durch alle gesammelten dynamic Routes
2. Sammle Routes und Handler in getrennten Arrays bis die maximale Chunksize erreicht wurde (Standard=10)
3. Beim sammeln werden die Dummy Placeholder der Route angehängt (aktuelle Platznummer - eigene Placeholder)
4. Wenn die Route mehr Placeholder besitzt als der aktuelle Platz der Regex wird diese Anzahl nun zur neuen Platznummer
5. Erhöhe die Platznummer um 1 und speichere den Handler an der gleichen Stelle wie die Regex
6. Wenn Chunk voll ist fasse die Routes zu einer Regex zusammen mit dem Muster: ````(?| Routes mit | dazwischen )````
7. Speichere die zusammengefasste Regex erneut in ein Array
8. Speichere die Handler zu den zusammengefassten Routes in ein Array
9. Danach setze die Sammler und Counter zurück und beginne erneut zu sammeln

````php
<?php
// Beispiel für das Route chunking
$this->routeList[] ='~^(?|' . implode('|', $this->routeCollector) . ')$~x';	//die gemeinsame Regex
$this->handlerList[]=$this->handleCollector; //die Handler zu den Routes
````

<br>

[hier gehts weiter mit: Mapping](routemapping.md)

### Inhalt Routing
[1. Start](index.md) <br>
[2. Router](router.md) <br>
[3. Dispatching](dispatching.md) <br>
[4. Route Creation](routeCreation.md) <br>
[5. Mapping](routemapping.md)
