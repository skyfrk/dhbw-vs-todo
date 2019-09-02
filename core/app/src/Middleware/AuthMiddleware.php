<?php

namespace App\Middleware;

use RedBeanPHP\R as R;
use App\Utils\Validations as Validation;
use Respect\Validation\Validator as v;
use Interop\Container\ContainerInterface;

class AuthMiddleware
{
    private $validator;

    public function __construct(ContainerInterface $container)
    {
        $this->validator = $container->get('validator');
    }

    public function __invoke($request, $response, $next)
    {
        try
        {
            $token = $request->getAttribute('token');

            $validation = $this->validator->validate($token, [
                'id' => Validation::id(),
                'scope' => v::objectType()->attribute('uid', v::intVal()->positive()),
                'iat' => v::intVal()->positive(),
                'exp' => v::intVal()->positive(),
                'ttl' => v::intVal()->positive()
            ]);

            if(!$validation->isValid())
            {
                return $response->withJson("JWT payload invalid", 403);
            }

            if($token['exp'] < time())
            {
                return $response->withJson("JWT expired", 403);
            }

            $tokenBean = R::load('jwt', $token['id']);

            if($tokenBean->id != $token['id'])
            {
                return $response->withStatus(403, "JWT not registered");
            }

            if(!filter_var($tokenBean->is_valid, FILTER_VALIDATE_BOOLEAN))
            {
                return $response->withStatus(403, "JWT invalid");
            }

            $userBean = R::load('user', $token['scope']->uid);

            if($userBean->id != $token['scope']->uid)
            {
                return $response->withStatus(403, "User not registered");
            }

            if($userBean->mail_confirmed != 1)
            {
                return $response->withStatus(403, "Mail not confirmed");
            }

            $tokenBean->date_last_used = new \DateTime();
            R::store($tokenBean);

            return $next($request, $response);
        } 
        catch (\Throwable $th)
        {
            return $response->withStatus(500, "Auth failed");
        }
    }
}
