<?php

namespace App\Services;

use Interop\Container\ContainerInterface;
use RedBeanPHP\R as R;
use App\Utils\RedBeanPHPExtensions as RE;
use App\Utils\Validations as Validation;
use Respect\Validation\Validator as v;

class TaskListService
{
    public function getTaskLists(int $userId, array $params)
    {
        $orderByStatement = $this->getOrderByStatement($params);
        $isFavoriteCondition = $this->getIsFavoriteCondition($params);

        $userBean = R::load('user', $userId);
        $taskListBeans = $userBean
            ->withCondition(" (title LIKE :title $isFavoriteCondition ) $orderByStatement LIMIT :limit OFFSET :offset ", [
                ':title' => $this->getTitleParameter($params),
                ':limit' => $params['limit'],
                ':offset' => $params['offset']]
            )
            ->ownTasklistList;

        $result = [];
        foreach ($taskListBeans as $taskListBean) {
            array_push($result, $this->generateTaskListArray($taskListBean));
        }
        return $result;
    }

    public function storeTaskList(int $userId, array $data)
    {
        $user = R::load('user', $userId);
        $taskListBean = R::dispense([
            '_type' => 'tasklist',
            'title' => $data['title'],
            'icon' => $data['icon'],
            'is_favorite' => false,
            'date_created' => new \DateTime()
        ]);
        $user->ownTaskListList[] = $taskListBean;

        R::store($user);
        
        return $this->generateTaskListArray($taskListBean);
    }

    public function getTaskListById(int $userId, int $taskListId)
    {
        $taskListBean = RE::load('tasklist', $taskListId);
        $this->checkBeanAccess($taskListBean, $userId);
        return $this->generateTaskListArray($taskListBean);
    }

    public function updateTaskListById(int $userId, int $taskListId, array $data)
    {
        $taskListBean = RE::load('tasklist', $taskListId);
        $this->checkBeanAccess($taskListBean, $userId);

        if(isset($data['title']))
        {
            $taskListBean->title = $data['title'];
        }

        if(isset($data['icon']))
        {
            $taskListBean->icon = $data['icon'];
        }

        if(isset($data['isFavorite']))
        {
            $taskListBean->is_favorite = $data['isFavorite'];
        }

        R::store($taskListBean);
        return $this->generateTaskListArray($taskListBean);
    }

    public function deleteTaskListById(int $userId, int $taskListId)
    {
        $taskListBean = RE::load('tasklist', $taskListId);
        $this->checkBeanAccess($taskListBean, $userId);

        $result = $this->generateTaskListArray($taskListBean);

        $taskBeans = $taskListBean->ownTaskList;
        R::trashAll($taskBeans);
        R::trash($taskListBean);

        return $result;
    }

    private function getOrderByStatement(array $params)
    {
        if(isset($params['sort_by']))
        {
            if(Validation::queryParamSortByTaskList()->validate($params['sort_by']))
            {
                $sort_order = $this->getSortOrder($params);
                return " ORDER BY " . $params['sort_by'] . " $sort_order ";
            }
        }

        return "";
    }

    private function getSortOrder(array $params)
    {
        if(isset($params['sort_order']))
        {
            if(strcasecmp($params['sort_order'], "desc") == 0)
            {
                return "DESC";
            }
        }

        return "ASC";
    }

    private function getTitleParameter(array $params)
    {
        if(!isset($params['title']))
        {
            return "%";
        }

        return '%' . $params['title'] . '%';
    }

    private function getIsFavoriteCondition(array $params)
    {
        if(!isset($params['is_favorite']))
        {
            return "";
        }

        if(filter_var($params['is_favorite'], FILTER_VALIDATE_BOOLEAN))
        {
            return 'AND is_favorite = true';
        }
        else
        {
            return 'AND is_favorite = false';
        }
    }

    private function generateTaskListArray($taskListBean)
    {
        return [
            'id' => $taskListBean->id,
            'title' => $taskListBean->title,
            'isFavorite' => boolval($taskListBean->is_favorite),
            'icon' => $taskListBean->icon,
            'dateCreated' => $taskListBean->date_created,
            'taskCount' => [
                'total' => R::count('task', 'WHERE tasklist_id = :taskListId', [ 'taskListId' => $taskListBean->id]),
                'open' => R::count('task', 'WHERE (tasklist_id = :taskListId AND state = :state)', [
                    'taskListId' => $taskListBean->id,
                    'state' => 0
                ]),
                'inProgress' => R::count('task', 'WHERE (tasklist_id = :taskListId AND state = :state)', [
                    'taskListId' => $taskListBean->id,
                    'state' => 1
                ]),
                'done' => R::count('task', 'WHERE (tasklist_id = :taskListId AND state = :state)', [
                    'taskListId' => $taskListBean->id,
                    'state' => 2
                ]),
                'aborted' => R::count('task', 'WHERE (tasklist_id = :taskListId AND state = :state)', [
                    'taskListId' => $taskListBean->id,
                    'state' => 3
                ])
            ]
        ];
    }

    private function checkBeanAccess($bean, $userId)
    {
        if($bean->user_id != $userId)
        {
            throw new \Exception("User not authorized to access taskList", 403);
        }
    }
}