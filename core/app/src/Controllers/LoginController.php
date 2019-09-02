<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Services\AuthService;
use App\Utils\Validations as Validation;
use Respect\Validation\Validator as v;

class LoginController 
{
    private $authService;
    private $validator;

    public function __construct(ContainerInterface $container)
    {
        $this->authService = $container->get('authService');
        $this->validator = $container->get('validator');
    }

    public function login(Request $request, Response $response)
    {
        $parsedBody = $request->getParsedBody();

        $validation = $this->validator->validate($parsedBody, [
            'mail' => v::email(),
            'password' => Validation::password(),
            'rememberMe' => v::boolVal()
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $result = $this->authService->getJwt($parsedBody['mail'], $parsedBody['password'], $parsedBody['rememberMe']);
            return $response->withJson($result, 200);
        }
        catch (\Throwable $th)
        {
            if($th->getCode() === 404)
            {
                return $response->withStatus(404, "Mail not registered");
            }

            if($th->getCode() === 403)
            {
                return $response->withStatus(403, "Mail not confirmed");
            }

            if($th->getCode() === 401)
            {
                return $response->withStatus(401, "Credentials incorrect");
            }

            return $response->withStatus(500);
        }
    }

    public function renew(Request $request, Response $response)
    {
        $userId = $request->getAttribute('token')['scope']->uid;

        try
        {
            return $response->withJson($this->authService->extendJwtTtl($request->getAttribute('token'), $userId), 200);
        }
        catch (\Throwable $th)
        {
            if($th->getCode() === 404)
            {
                return $response->withStatus(404, "JWT not found");
            }

            if($th->getCode() === 403)
            {
                return $response->withStatus(403, "JWT access denied");
            }

            return $response->withStatus(500);
        }
    }
}