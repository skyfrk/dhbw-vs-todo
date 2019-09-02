<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;
use App\Utils\Validations as Validation;

class TaskListController 
{
    private $taskListService;
    private $taskService;
    private $validator;
    private $reportService;
    
    public function __construct(ContainerInterface $container)
    {
        $this->taskListService = $container->get('taskListService');
        $this->taskService = $container->get('taskService');
        $this->validator = $container->get('validator');
        $this->reportService = $container->get('reportService');
    }

    public function getTaskLists(Request $request, Response $response)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $params = $request->getQueryParams();

        $validation = $this->validator->validate($params, [
            'limit' => Validation::queryParamLimit(),
            'offset' => Validation::queryParamOffset(),
            'is_favorite' => v::optional(v::boolVal()),
            'title' => v::optional(Validation::queryParamTitle()),
            'sort_by' => v::optional(Validation::queryParamSortByTaskList()),
            'sort_order' => v::optional(Validation::queryParamSortOrder())
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $taskLists = $this->taskListService->getTaskLists($uid, $params);
            return $response->withJson($taskLists, 200);
        } 
        catch (\Throwable $th)
        {
            return $response->withStatus(500);
        }
    }

    public function createTaskList(Request $request, Response $response)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $data = $request->getParsedBody();

        $validation = $this->validator->validate($data, [
            'title' => Validation::taskListTitle(),
            'icon' => Validation::taskListIcon()
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $taskList = $this->taskListService->storeTaskList($uid, $data);
            return $response->withJson($taskList, 201);
        }
        catch (\Throwable $th)
        {
            return $response->withStatus(500);
        }
    }

    public function getTaskList(Request $request, Response $response, array $args)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $taskListId = $args['id'];

        if(!Validation::id()->validate($taskListId))
        {
            return $response->withStatus(400);
        }

        try 
        {
            $taskList = $this->taskListService->getTaskListById($uid, $taskListId);
            return $response->withJson($taskList, 200);
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

    public function updateTaskList(Request $request, Response $response, array $args)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $taskListId = $args['id'];
        $data = $request->getParsedBody();

        if(!Validation::id()->validate($taskListId))
        {
            return $response->withStatus(400);
        }

        $validation = $this->validator->validate($data, [
            'title' => v::optional(Validation::taskListTitle()),
            'icon' => v::optional(Validation::taskListIcon()),
            'isFavorite' => v::optional(Validation::taskListIsFavorite())
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $taskList = $this->taskListService->updateTaskListById($uid, $taskListId, $data);
            return $response->withJson($taskList, 200);
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

    public function deleteTaskList(Request $request, Response $response, array $args)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $taskListId = $args['id'];

        if(!Validation::id()->validate($taskListId))
        {
            return $response->withStatus(400);
        }

        try
        {
            $taskList = $this->taskListService->deleteTaskListById($uid, $taskListId);
            return $response->withJson($taskList, 200);
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

    public function getTasks(Request $request, Response $response, array $args)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $taskListId = $args['id'];

        $params = $request->getQueryParams();

        $validation = $this->validator->validate($params, [
            'limit' => Validation::queryParamLimit(),
            'offset' => Validation::queryParamOffset(),
            'title' => v::optional(Validation::queryParamTitle()),
            'state' => v::optional(Validation::taskState()),
            'priority' => v::optional(Validation::taskPriority()),
            'sort_by' => v::optional(Validation::queryParamSortByTask()),
            'sort_order' => v::optional(Validation::queryParamSortOrder())
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        if(!Validation::id()->validate($taskListId))
        {
            return $response->withStatus(400, "Bad taskListId");
        }

        $params['tasklist'] = $taskListId;

        try
        {
            $task = $this->taskService->getTasks($uid, $params);
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

    public function createTask(Request $request, Response $response, array $args)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $taskListId = $args['id'];
        $data = $request->getParsedBody();

        if(!Validation::id()->validate($taskListId))
        {
            return $response->withStatus(400, "Bad taskListId");
        }

        $validation = $this->validator->validate($data, [
            'title' => Validation::taskTitle(),
            'description' => v::optional(Validation::taskDescription()),
            'priority' => v::optional(Validation::taskPriority()),
            'dateDeadline' => v::optional(Validation::taskDateDeadline())
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $task = $this->taskService->createTask($uid, $taskListId, $data);
            return $response->withJson($task, 201);
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

    public function getReport(Request $request, Response $response, array $args)
    {
        $uid = $request->getAttribute('token')['scope']->uid;
        $params = $request->getQueryParams();
        $taskListId = $args['id'];

        if(!Validation::id()->validate($taskListId))
        {
            return $response->withStatus(400, "Bad taskListId");
        }

        $validation = $this->validator->validate($params, [
            'days' => Validation::reportDays()
        ]);

        if(!$validation->isValid())
        {
            return $response->withJson($validation->getErrors(), 400);
        }

        try
        {
            $report = $this->reportService->getTaskListReport($uid, $taskListId, $params['days']);
            return $response->withJson($report, 200);
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