# CHANGELOG

# 1.8.x

## 1.8.2

- since 2020/09/23
- DispatcherInterface hat nur noch die function dispatch() (internal bc break)
- Route Object baut nun ihre Middleware von route und von group selber zusammen, das aber nur einmal (internal)
- Route Object ermittelt selbstständig die strategy (internal)
- router gibt nun immer das route object und nicht mehr nur den handler zurück (internal)
- generator trait chunkt nun immer routes, egal was die chunk size zurück gibt. (internal)
- In der App wird die Route Middleware direkt bei build() dem middleware array hinzugefügt (internal)
- queue class hat eine neue function: addMultiple() bei der mehrere mw auf einaml hinzugefügt werden können (internal)
 

## 1.8.1

- since 2020/08/24
- ReadMe Credits wieder hinzugefügt
- GeneratorInterface hat nur die die method generate (internal bc break)
- [#11](https://gitlab.com/grammm/php-gram/phpgram/-/issues/11) Middleware und Strategy werden mit einem static Array gesammelt und nicht mehr mit einem extra Collector

## 1.8.0

- since 2020/08/07
- Std middleware werden nun direkt in der App class gesammelt und nicht mehr im Collector
- [#7](https://gitlab.com/grammm/php-gram/phpgram/-/issues/7) Bei einem aufruf von Route group wird der collector mitgegeben
- [#9](https://gitlab.com/grammm/php-gram/phpgram/-/issues/9) Alle Attribute im Request werden nun durch const's repräsentiert
- [#6](https://gitlab.com/grammm/php-gram/phpgram/-/issues/6) Der QueueHandler lässt sich nun auch als callable aufrufen
- [#8](https://gitlab.com/grammm/php-gram/phpgram/-/issues/8) [bc break!] Emitter wird nicht mehr unterstüzt. Bei `start()` wird nur noch ein fertiges ResponseInterface zurück gegeben

# 1.7.x

## 1.7.0

- since 2020/02/20
- Router Bug fix:
	- Unterstüzt jetzt auch optionale Parameter
	- nun kann bei den Static Routes (ohne Parameter) auch Urls mit einem . gematcht werden
	- Route Data wird dem Dispatcher im Constructor übergeben
- App Constructor ist nun nicht mehr private
- QueueHandler ist nun auch ein Interface

# 1.6.x

## 1.6.2
- since 2020/01/27
- Memory Leaks beim QueueHandler fix

## 1.6.1
- since 2020/01/26
- Dispatcher Warning wenn keine Routes gesetzt sind entfernt

## 1.6.0

- since 2020/01/23
- Middleware werden nun in einem Queue Object gespeichert	
	- dieses wird bei jedem Request erstellt
- Async App entfernt, da die normale App somit auch Async Ready ist
- rawOptions in der App Class entfernt
- Debug Mode nur noch auf true oder false setzen:
	- bei true -> render exception
	- bei false -> nur Response mit 500 zurück geben
- Bei 404 bzw. 405 wird nun der richtige Statuscode bei Exceptions ausgegeben, sollte kein Handler gesetzt sein
- Resolver müssen nun bei resolve() ein array mit den Paramtern bekommen


# 1.5.x

## 1.5.3

- since 2020/01/18
- Routing Middleware:
	- Bereiche mit Methods aufgeteilt, damit die Async Mw nicht alles doppelt machen muss
- Dispatcher: 
	- bei 405 bzw. 404 werden doppelte Http Methods nur noch einmal durchsucht
- QueueHandler unterstüzt nun auch callable Middleware
- Interfaces angepasst
- adMiddle() zu addMiddleware() umbenannt

## 1.5.2
- since 2020/01/13
- CallableResolver aufgeteilt in ClosureResolver und CallableResolver
	- ClosureResolver ist für anonymous functions
	- CallableResolver für alle anderen callable, wie Klassen mit der Method __invoke()
- App gibt jetzt auch den Psr 11 Container wieder zurück
- Router: als Standard Generator und Dispatcher werden nun MarkBased verwendet

## 1.5.1
- since 2020/01/03
- Router:
	- Es wird jetzt nur noch MethodSort genutzt
	- Es wird in der Routemap nur noch die Route Id gespeichert. (dadurch ist diese kleiner und verbraucht weniger Ram)
	- Router holt sich die Infos aus dem Routeobject
	- MarkBased generator und dispatcher hinzugefügt

## 1.5.0
- since 2019/12/30
- Async Requests:
	 - Es werden jetzt auch ohne eine Erweitertung Async Requests unterstüzt
	 - Dazu einfach die AsyncApp anstatt der normalen App Class verwenden
- Route:
	- Parsen der Route findet nun im Generator und nicht mehr direkt in der Route statt
	- Es wurde ein allgemeiner Generator dafür erstellt
	- Die normalen Generatoren und Dispatcher sind nun im Ordner Std
- QueueHandler ist nun im Middleware Ordner und im Middleware Namespace
- Allgemein Code Quality verbessert
- Route Collector: addGroup() wurde zu group() umbenannt
- Strategy:
	- Erstellt nun den Response
	- Ein Response mit string Content kann mithilfe eines Traits erstellt werden
	- Für alle anderen Inhalte muss entweder eine neue Strategy geschrieben werden oder der Response direkt zurückgegeben werden
	- Buffer Strategy kann nun auch einen Response aus dem Buffer zurück geben

# 1.4.x

## 1.4.2
- since 2019/12/14
- Möglichkeit Exceptions an zuzeigen eingebaut. Nun wird nur noch wenn gewünscht die Exception ausgegeben
- Strategies können nun auch aus dem Container geladen werden

## 1.4.1
- since 2019/12/09
- Es kann nun auch auf ResponseCreator, zur Laufzeit, zugegriffen werden, mit `getResponseCreator()`
	- Vorher müssen aber die Factories gesetzt sein
	- Sollte eine andere Strategy verwendet werden muss diese auch vorher gesetzt sein
	- Lösung: ResponseCreator in den Psr 11 Container legen und alle Middleware die diesen brauchen ebenfalls in den Container
	- ResponseCreator wird zur Laufzeit erstellt in der App, dieser Container sollte das ebenfalls mit dem ResponseCreator tun
	
## 1.4.0
- since 2019/12/06
- neuer Route Generator und Dispatcher hinzugefügt
	- kann optional genutzt werden
	- Generator ordnet Routes bei ihrer Http Method ein. 
	- Sollte eine Route mehrere Http Methods haben wird diese mehrfach in der Routemap mit ihrer Id und Group Id auftauchen
	- gut geeignet für opcache
- NotFoundHandler wirft nun Exceptions, wenn kein Handler angegeben wurde


# 1.3.x

## 1.3.1
- since 2019/11/05
- ResolverCreator kann nur noch am Anfang gesetzt werden
- Middleware Order (außer Betrieb) entfernt

## 1.3.0
- since 2019/11/01
- Response Creator und Route Mw update:
	- speichert Werte nun nicht mehr in Klassen Variablen ab
- App erstellt Objecte anders
- App ist nun RequestHandlerInterface
- Router benutzt nun keine Klassen Variablen zum Zwischenspeichern mehr
- ResolveCreator gibt nun den Resolver direkt zurück ohne zwischen Function
- Doc Anpassung
- alle php core Functions mit \ versehen, um sicher zustellen, 
dass auch die Core Functions verwendet werden 


# 1.2.x

## 1.2.7
- since 2019/10/27
- Class Resolver erstellt für die aufzurufende class und reflactor keine Klassenvariable mehr
sondern eine lokale
- Routing Mw packt nun nicht mehr den rohen Router handler in den Request
- Emitter bugfix update

## 1.2.6
- since 2019/10/15
- Buffer Strategy Php Notice bugfix

## 1.2.5
- since 2019/10/13
- Router Basepath und Startpage bugfix

## 1.2.4
- since 2019/10/05
- Exceptions ausgeweitet
- Mw Handler erben nun vom ClassInterface und haben somit Zugriff auf Request und Response

## 1.2.3
- since 2019/10/05
- Middleware können jetzt auch aus einem Psr 11 Container geladen werden
und nur ihr Index braucht angegeben zu werden

## 1.2.2

- since 2019/10/04
- Alle Strategies haben nun protected Methods und Variables um diese ggf. zu erweitern
- Head Routes wieder hinzugefügt

## 1.2.1

- since 2019/09/26
- App Class jetzt mit init Function
- eine public sendRequest() Method wie bei Psr 18 die aber ein ServerRequestInterface akzeptiert
- Diese kann aufgerufen werden wenn nur ein Response zurück gegeben werden soll ohne den zu emitten

## 1.2.0

- since 2019/09/24
- Möglichkeit Response in der Applikation zu ändern
- DI hinzugefügt für Classen die das ClassInterface implementiert haben
- DI Funktioniert mit Psr 11
- Es wird im Container zuerst nach der auf zurufenden Klasse gesucht und danach werden alle Parameter des Constructor 
der Klasse mit Namespace und danach ohne Namespace im Container gesucht
- Functions können mit ``$this->value`` auf die Values im Container zugreifen. 
- Klassen können auch wie Functions mit ``$this->value`` auf einen Value im Container zugreifen (dies ist ein Anti Pattern)


# 1.1.x

## 1.1.5

- since 2019/09/23
- ResponseHandler in ResponseCreator umbenannt
- Möglichkeit ResponseCreator und QueueHandler in den App Einstellungen zu setzen

## 1.1.4

- since 2019/09/22
- Klassen müssen nicht mehr von Controller erben sondern nur noch ein Interface implementieren.
- Für dieses Interface gibt es ein Trait, dass die Methods und Variables bereits implementiert hat. Dies kann dann einfach mit ``use`` genutzt werden

## 1.1.3

- since 2019/09/21
- JsonStrategy:
	- Möglichkeit Json Optionen zu setzen über den Konstruktor

## 1.1.2

- since 2019/09/21
- Router: 
    - der letzte ``/`` wird nun immer ignoriert, es sei denn es wurde in den Routeroptionen anders fest gelegt
    - check Http Method lässt sich nun über Otionen mit ``check_method`` aus stellen

## 1.1.1 

- since 2019/09/19
- Doc Update

## 1.1.0

- since 2019/09/19
- Controller können nun den Request wirkungsvoll für den ResponseHandler verändern