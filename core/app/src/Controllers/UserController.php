<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Services\AuthService;
use App\Utils\Validations as Validation;
use Respect\Validation\Validator as v;

class UserController 
{
    private $authService;
    private $validator;

    public function __construct(ContainerInterface $container)
    {
        $this->authService = $container->get('authService');
        $this->validator = $container->get('validator');
    }

    public function getValidJwts(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $userId = $request->getAttribute('token')['scope']->uid;

        $validation = $this->validator->validate($params, [
            'limit' => Validation::queryParamLimit(),
            'offset' => Validation::queryParamOffset()
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            return $response->withJson($this->authService->getValidJwts($params, $userId), 200);
        }
        catch (\Throwable $th)
        {
            return $response->withStatus(500);
        }
    }

    public function invalidateJwt(Request $request, Response $response, array $args)
    {
        $tokenId = $args['id'];
        $password = $request->getParsedBody()['password'];
        $userId = $request->getAttribute('token')['scope']->uid;

        if(!Validation::id()->validate($tokenId) || !Validation::password()->validate($password))
        {
            return $response->withStatus(400);
        }

        try
        {
            $this->authService->invalidateJwt($tokenId, $userId, $password);
            return $response->withStatus(200);
        }
        catch (\Throwable $th)
        {
            if($th->getCode() === 401)
            {
                return $response->withStatus(401, "Password incorrect");
            }

            if($th->getCode() === 403)
            {
                return $response->withStatus(403, "JWT access denied");
            }

            if($th->getCode() === 404)
            {
                return $response->withStatus(404, "JWT not found");
            }

            return $response->withStatus(500);
        }
    }
}