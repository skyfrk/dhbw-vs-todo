# Plan für die Infrastruktur

## Docker zum lokalen Entwickeln

Für beide Repos (core und frontend) werden wir uns ein Dockerfile bauen, welches uns eine konsistente Dev-Umgebung zur Verfügung stellt. (z.B. LAMP stack)

## Repos

Die repos liegen in Azure und wir haben branch policies, die unseren git flow sicherstelen (master, develop, feature branches).

## Builds

Wenn gepusht wird auf einen feature branch dann wird ein build ausgelöst, der die app baut und testet.

Auch wenn ein PR auf develop / master gestartet wird wird eine build ausgelöst.

## Deployments

Wir werden frontend und core als docker container automatische in azure deployen.

Dazu müssen wir evtl noch db-init scripts etc schreiben.

## Epics und Tasks

Die Aufgaben, die wir haben während wir entwickeln werden wir als tasks und epics in DevOps verwalten.

## Tests / quick code share

wenn man nur schnell was testen will und dazu den code sharen will kann man das dev-playground repo benutzen.