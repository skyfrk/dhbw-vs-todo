# Improvements

Nicht alles ist perfekt im Backend - vorallem der Zeit geschuldet.

## Features

Man könnte noch unzählige hinzufügen :D - man vergleiche mit TODO-Apps wie z.B. Google Keep...

## Codebase

* Unit-Tests hinzufügen
  * damit man sich sicherer beim releasen nach schnellen änderungen sein kann
* Dependency-Injection ordentlicher durchziehen
* Backend in einer schöneren Sprache als PHP schreiben :P

## DevOps / SRE

* Azure Release Pipeline für Infrastructure
* Integration-Tests hinzufügen
* Deployment-Tests (Smoke-Tests) hinzufügen
* Konfiguration vom Soure-Code entfernen und über Umgebungsvariablen regeln (Über einen Azure-Keyvault bzw. Build-Variables)
* Builds in Azure Pipeline auf YAML-Builds umstellen, damit sie mit im Source-Code dokumentiert sind
* [VS Code DevContainer](https://code.visualstudio.com/docs/remote/containers) den Repos hinzufügen
  * dann muss man nicht mehr manuell die docker-compose ausführen
* Den Builds License und Security Scans hinzufügen

## Architektur

* Filtering und Sorting der API verbessern durch Implementierung von z.B. [OData](https://www.odata.org/)
* Externen SMTP Server durch REST-Service ablösen (SMTP Port ist öfter gesperrt als der HTTP/HTTPS-Port)