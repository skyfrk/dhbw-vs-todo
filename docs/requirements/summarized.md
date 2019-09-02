# Webanwendung zur Verwaltung von Aufgaben

## Aufgabe/n

Eine Aufgabe hat folgende Eigenschaften:

- Titel
- Beschreibung
- Gewicht (1 - 5)
- Zeitpunkt bis Zur erledigung (optional)
- Status über Fortschritt (erledigt, in Bearbeitung, offen, verspätung Erledigt, abgebrochen)
  - Der Wert für das Gewicht und die anzahl der Stati soll vorgegeben sein (Benutzer kann kein Custom Status einfügen)
- Nur der Ersteller der Aufgabenliste kann die Aufgaben sehen
- Erledigte Aufgaben sind nicht änderbar und nicht direkt löschbar

Sortierbar und filterbar nach:

- Aufgabenliste
- Aufgabenstatus
- Dringlichkeit

### Dringlichkeit

Bestimmt sich aus:

1. Endetermin (kein Endetermin -> alle mit Endetermin sind wichtiger)
2. Gewicht

## Aufgabenliste

- Fasst mehrere Aufgaben zusammen
- Eine Aufgabe gehört zu nur exakt **einer** List
- Nur der Ersteller der Liste, kann die Liste sehen 
- Die gesammte Liste kann gelöscht werden

## Anwender

- Ein Anwender kann mehrere Aufgabenlisten haben
- Zur Erstellung von Listen muss man angemeldet sein
- Unangemeldete Anwender müssen sich vor benutzung erst Registrieren

## Oberfläche

- Die Aufgabenlisten sollen nach Aufgabenliste, Aufgabenstatus und Dringlichkeit sortierbar sein
- Aufgaben sollen Filterbar sein (Suche)
- Auswertungsmodul (Dashboard) soll Infos über eine Aufgabenliste liefern
- Die Anwendung muss als SPA umgesetzt sein