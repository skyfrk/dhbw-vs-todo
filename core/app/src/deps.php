<?php

$container = $app->getContainer();

$container['jwtMiddleware'] = function ($c)
{
    return new Tuupola\Middleware\JwtAuthentication($c->get('settings')['jwt']);
};

$container['authMiddleware'] = function ($c)
{
    return new App\Middleware\AuthMiddleware($c);
};

$container['corsMiddleware'] = function ($c)
{
    return new App\Middleware\CorsMiddleware($c);
};

$container['emailService'] = function ($c)
{
    return new App\Services\EmailService($c->get('settings')['emailService']);
};

$container['authService'] = function ($c)
{
    return new App\Services\AuthService($c);
};

$container['taskService'] = function ($c)
{
    return new App\Services\TaskService();
};

$container['taskListService'] = function ($c)
{
    return new App\Services\TaskListService();
};

$container['reportService'] = function ($c)
{
    return new App\Services\ReportService();
};

$container['registerService'] = function ($c) 
{
    return new App\Services\RegisterService($c);
};

$container['validator'] = function ($c)
{
    return new Awurth\SlimValidation\Validator(false);
};

// RedBean setup (RedBean is 100% static and therefore not in the DI container.)
$db_settings = $container->get('settings')['db'];
\RedBeanPHP\R::setup(
    $db_settings['dsn'],
    $db_settings['user'],
    $db_settings['password']
);
\RedBeanPHP\R::freeze($db_settings['freeze']);