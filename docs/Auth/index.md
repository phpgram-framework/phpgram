# Authentifizierung
- Eine Hilfe um einen Nutzer zu authentifizieren
- Besitzt verschiedene Funktionen um auch Nutzer zu registrieren, zu bearbeien, mit Cookie anzumelden oder das Passwort zu ändern
- Passwörter werden speziel verschlüsselt
- Benötigt ein User Model das ein bestimmtes Interface implementiert hat
- läd die benötigten Daten aus der DB schon beim ersten aufruf oder
- Das Model kann auch schon mit Daten aufgefüllt sein und übergeben werden

## Is Login
- Prüft ob ein User eingelogt ist:
1. Prüfe ob der unsername und die userid in der Session zu finden sind ist der user eingelogt, gebe 1 zurück
2. Prüfe ob es einen Cookie gibt mit der Session_id und einen mit dem token
3. Wenn es diese Cookies gibt gleiche die Werte mit einer Tabelle in der DB ab
4. Wenn abgleich erfolgreich, lade username und userid in die Session und ändere die beiden Cookiewerte in der Db und setze den Cooke neu
5. Gebe 2 zurück für Cookie
6. Wenn weder in der Session noch im Cookie etwas zu finden ist gebe 0 zurück
- die unterschiedlichen Zahlen können in der Middleware verwendet werden, 
- z. B. um nach einem Cookielogin noch mehr Werte in die Session zu laden

## Login
- Führt den Loginprozess aus
1. Lade, wenn nicht schon passiert, Userinformationen in das Model
2. und checke dabei gleichzeitig ob es diesen Nutzernamen gibt
3. Wenn nicht gebe false zurück
4. Prüfe das Passwort über das User Model, mit der Verschlüsselung
5. Wenn beides stimmt, setze usrname und userid in die Session ein.
6. Wenn cookie ausgewählt wurde erstelle einen Cookie mit einer Session_id und einem Token
7. Beide Werte werden auch in die DB gespeichert

## Register
- Registriert einen Nutzer
1. Verschlüsselt das Passwort
2. Kann prüfen ob der Username schon existiert (oder auch nciht falls die Prüfung schon wo anders statt fand z. B. wenn ein username generiert wird)
3. Füge einen neuen Datensatz mit dem User model hinzu

## Cahnge Pw
- Ändert das Passwort
1. Prüft zuerst ob das alte Passwort stimmt (die Abfrage kann nur hier passieren)
2. wenn ja update die Datenbank mit dem neuen verschlüsselten Passwort

## CSRF Token prüfen
- erstellt und prüft ein generiertes Token, dass in einem Formular angegeben werden kann und auch in die Session gesetzt wird