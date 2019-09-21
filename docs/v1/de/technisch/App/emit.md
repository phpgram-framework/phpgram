# Emitter

- emit den Response

- emitte die header im Response mit: ``header();``

	- prüfe zuerst ob bereits headers versendet wurden, wenn ja throw Exception

	- Status Line

	- App Headers wie Content-Typ

- emitte den Body

	- der Stream wird mit einer bestimmten Bitanzahl ausgegeben

	- wenn der header Content-Typ gesetzt wurde wird die maxmiale Anzahl der Bits, die aufeinmal ausgegeben werden darf neu ermittelt

	- der Content wird, sollte er Readable sein mit einer Schleife mit ``echo`` ausgegeben

- danach wird die Seite geschlossen mit ``exit()`` da nach dem Absenden der Header keine ausgabe erfolgen soll

````php
<?php
namespace Gram\App;
class Emitter
{
	
}
````
