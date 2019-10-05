# CHANGELOG

# 1.2.x

## 1.2.4
- since 201/10/05
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