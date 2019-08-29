# Dispatching
Es gibt zwei Dispatcher:
- Static
- Dynamic
- Routes und Handler befinden sich in unterschiedlichen Arrays (siehe Route Creation)
## Static Dispatcher
- Klasse: 
```php
<?php 
namespace Gram\Route\Dispatcher;
class StaticDispatcher implements Dispatcher
{
	
}
```
1. Prüft ob es die Rpute als Index in dem Route Array gibt
2. Wenn die Static Route mit der Request Url übereinstimmt gebe den Handler ohne Parameter zurück
## Dynamic Dispatcher
- Klasse: 
```php
<?php 
namespace Gram\Route\Dispatcher;
class DynamicDispatcher implements Dispatcher
{
	
}
```
1. Durchläuft die Regexliste und sucht jeden Eintrag ab (siehe Route Creation)
2. Wenn eine Regex passt wird der Handler zurück gegeben
3. Der Index des Handlers, im Handler Array, ist der gleiche wie der Platz wo die Regex gefunden wurde (Anzahl an Matches) 
`````php
<?php
$i = 0; //Welche Regexliste
$matches = array("Match1","Match2"); //die Matches
$handle = $this->handler[$i][count($matches)];
`````

<br>

[hier gehts weiter mit: Route Creation](routeCreation.md)

### Inhalt Routing
[1. Start](index.md) <br>
[2. Router](router.md) <br>
[3. Dispatching](dispatching.md) <br>
[4. Route Creation](routeCreation.md) <br>
[5. Mapping](routemapping.md)