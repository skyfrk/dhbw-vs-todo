# Authentication

Ein Nutzer callt mit `mail`, `password` und `displayName` die Route `/register`. Der Nutzer bekommt dann einen token per email, der an `/register/confirm` gesendet werden muss.

Jetzt kann sich der User einloggen mit `mail` und `password` an der Route `/login`. Dabei bekommt er einen [JWT (Json Web Token)](https://jwt.io/introduction/).

Der JWT wird die `userId` beinhalten. Bei jeder Anfrage, die der User an einer geschützen Route stellt, wird vorher überprüft ob der JWT gültig ist und ob die `userId` auch entsprechende Rechte hat.

Der JWT muss vom Client lediglich gespeichert werden und bei jeder Anfrage über den Authorization Header mitgeschickt werden (`Authorization: Bearer <token>`).

## Links

* [JWT introduction](https://jwt.io/introduction/)
* [Auth0 Artikel wie man JWT als API tokens nutzen kann](https://auth0.com/blog/using-json-web-tokens-as-api-keys/)
* [Die middleware, die ich im backend dafür benutzen werde](https://github.com/tuupola/slim-jwt-auth)