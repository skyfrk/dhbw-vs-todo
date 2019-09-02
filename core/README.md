# TODO Core

## Local development guide

This guide targets developing in [Visual Studio Code](https://code.visualstudio.com/).

### Initial setup

1. Install [php](https://www.php.net/downloads.php) and [composer](https://getcomposer.org/) and run `composer install` in `app/deps` (Rerun `composer install` if dependencies have changed). You can also run `docker run --rm --interactive --tty --volume ${pwd}:/app composer install` in `app/deps`.
1. Install [docker](https://www.docker.com/get-started) and [docker-compose](https://docs.docker.com/compose/install/).
1. Run `docker-compose up --build` in the root directory of this repository to run the core on `localhost:6001` and adminer on `localhost:6002`.

### Debugging

1. Install the Visual Studio Code extension [PHP Debug](https://github.com/felixfbecker/vscode-php-debug).
2. Set your local IP in `docker-compose.yml` (`XDEBUG_CONFIG=remote_host=<your local ip here>`). If this IP is not set correctly the XDebug debugger will not be able to reach your editor.
3. Hit `F5` in Visual Studio Code and start debugging by requesting a page from the apache container :)

### Settings

In case something doesn't work like expected (e.g. STMP relay not reachable) go ahead and update `./app/src/settings.php`.
