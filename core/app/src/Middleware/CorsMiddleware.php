<?php

namespace App\Middleware;

use Interop\Container\ContainerInterface;

class CorsMiddleware
{
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke($req, $res, $next)
    {
        $response = $next($req, $res);
        return $response
            ->withHeader('Access-Control-Allow-Origin', $this->container->get('settings')['app']['frontendBaseUrl'])
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    }
}
