<?php
require './deps/vendor/autoload.php';

$settings = require './src/settings.php';

$app = new \Slim\App($settings);

require './src/deps.php';
require './src/middleware.php';
require './src/routes.php';

$app->run();

\RedBeanPHP\R::close();
