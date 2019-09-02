<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use RedBeanPHP\R as R;
use App\Services\EmailService;
use App\Utils\Validations as Validation;
use Respect\Validation\Validator as v;

class RegisterController 
{
    private $validator;
    private $registerService;

    public function __construct(ContainerInterface $container)
    {
        $this->validator = $container->get('validator');
        $this->registerService = $container->get('registerService');
    }

    public function register(Request $request, Response $response)
    {
        $parsedBody = $request->getParsedBody();

        $validation = $this->validator->validate($parsedBody, [
            'mail' => v::email(),
            'displayName' => Validation::displayName(),
            'password' => Validation::password()
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $this->registerService->registerUser($parsedBody['mail'], $parsedBody['password'], $parsedBody['displayName']);
            return $response->withStatus(201);
        }
        catch (\Throwable $th)
        {
            if($th->getCode() === 409)
            {
                return $response->withStatus(409, "User already registered");
            }

            return $response->withStatus(500);
        }
    }

    public function confirm(Request $request, Response $response, array $args)
    {
        $validation = $this->validator->validate($args, [
            'token' => v::notEmpty()
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $this->registerService->confirmRegistration($args['token']);
            return $response->withStatus(200);
        }
        catch (\Throwable $th)
        {
            if($th->getCode() === 404)
            {
                return $response->withStatus(404, "User not registered");
            }

            if($th->getCode() === 409)
            {
                return $response->withStatus(404, "User already confirmed");
            }

            if($th->getCode() === 409)
            {
                return $response->withStatus(403, "Registration token expired");
            }

            return $response->withStatus(500);
        }  
    }

    public function resend(Request $request, Response $response, array $args)
    {
        $parsedBody = $request->getParsedBody();

        $validation = $this->validator->validate($parsedBody, [
            'mail' => v::email()
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $this->registerService->resendRegistrationMail($parsedBody['mail']);
            return $response->withStatus(200);
        }
        catch (\Throwable $th)
        {
            if($th->getCode() === 404)
            {
                return $response->withStatus(404, "User not registered");
            }

            if($th->getCode() === 409)
            {
                return $response->withStatus(409, "User already confirmed");
            }

            return $response->withStatus(500);
        }
    }
}