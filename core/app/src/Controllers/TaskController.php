<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use RedBeanPHP\R as R;
use App\Utils\Validations as Validation;
use Respect\Validation\Validator as v;

class TaskController 
{
    private $taskService;
    private $validator;
    
    public function __construct(ContainerInterface $container)
    {
        $this->taskService = $container->get('taskService');
        $this->validator = $container->get('validator');
    }

    public function getTasks(Request $request, Response $response)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $params = $request->getQueryParams();

        $validation = $this->validator->validate($params, [
            'limit' => Validation::queryParamLimit(),
            'offset' => Validation::queryParamOffset(),
            'title' => v::optional(Validation::queryParamTitle()),
            'state' => v::optional(Validation::taskState()),
            'priority' => v::optional(Validation::taskPriority()),
            'tasklist' => v::optional(Validation::id()),
            'sort_by' => v::optional(Validation::queryParamSortByTask()),
            'sort_order' => v::optional(Validation::queryParamSortOrder())
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $tasks = $this->taskService->getTasks($uid, $params);
            return $response->withJson($tasks, 200);
        }
        catch (\Throwable $th)
        {
            if($th->getCode() === 404)
            {
                return $response->withStatus(404);
            }

            if($th->getCode() === 403)
            {
                return $response->withStatus(403);
            }

            return $response->withStatus(500);
        }
    }

    public function getTask(Request $request, Response $response, array $args)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $taskId = $args['id'];

        if(!Validation::id()->validate($taskId))
        {
            return $response->withStatus(400, "Bad taskId");
        }

        try
        {
            $task = $this->taskService->getTaskById($uid, $taskId);
            return $response->withJson($task, 200);
        }
        catch (\Throwable $th)
        {
            if($th->getCode() === 404)
            {
                return $response->withStatus(404);
            }

            if($th->getCode() === 403)
            {
                return $response->withStatus(403);
            }

            return $response->withStatus(500);
        }
    }

    public function updateTask(Request $request, Response $response, array $args)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $taskId = $args['id'];
        $data = $request->getParsedBody();

        if(!Validation::id()->validate($taskId))
        {
            return $response->withStatus(400, "Bad taskListId");
        }

        $validation = $this->validator->validate($data, [
            'title' => v::optional(Validation::taskTitle()),
            'description' => v::optional(Validation::taskDescription()),
            'priority' => v::optional(Validation::taskPriority()),
            'dateDeadline' => v::optional(Validation::taskDateDeadline()),
            'state' => v::optional(Validation::taskState())
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $task = $this->taskService->updateTask($uid, $taskId, $data);
            return $response->withJson($task, 200);
        }
        catch (\Throwable $th)
        {
            if($th->getCode() === 404)
            {
                return $response->withStatus(404);
            }

            if($th->getCode() === 403)
            {
                return $response->withStatus(403);
            }

            return $response->withStatus(500);
        }
    }

    public function deleteTask(Request $request, Response $response, array $args)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $taskId = $args['id'];

        if(!Validation::id()->validate($taskId))
        {
            return $response->withStatus(400, "Bad taskId");
        }

        try
        {
            $task = $this->taskService->deleteTaskById($uid, $taskId);
            return $response->withJson($task, 200);
        }
        catch (\Throwable $th)
        {
            if($th->getCode() === 404)
            {
                return $response->withStatus(404);
            }

            if($th->getCode() === 403)
            {
                return $response->withStatus(403);
            }

            return $response->withStatus(500);
        }
    }
}