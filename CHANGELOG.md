# CHANGELOG

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