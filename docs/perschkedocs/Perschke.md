# Perschke

Hier kommt alles bzgl. der Abgabe rein. Die Markdown files können wir dann ez in ne pdf für ihn konvertieren.

## How to: Artefakte vorbereiten

### Backend

Settings anpassen:

```
// db
        'db' => [
            'user' => 'root',
            'password' => '',
            'dsn' => 'mysql:host=localhost;dbname=todo',
            'freeze' => false // set to true in production
        ],
```

### Frontend

.htaccess anpasssen

```
RewriteEngine On
    # If an existing asset or directory is requested go to it as it is
    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} -f [OR]
    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} -d
    RewriteRule ^ - [L]

    # If the requested resource doesn't exist, use index.html
RewriteRule ^ index.html
```

build: `ng build --prod --baseHref "/frontend/"`

