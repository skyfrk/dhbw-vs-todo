<?php

namespace App\Services;

use Interop\Container\ContainerInterface;
use RedBeanPHP\R as R;
use App\Utils\RedBeanPHPExtensions as RE;
use App\Utils\Validations as Validation;
use Respect\Validation\Validator as v;

class TaskService
{
    public function getTasks(int $userId, array $params)
    {
        $userBean = RE::load('user', $userId);

        $orderByStatement = $this->getOrderByStatement($params);
        $stateCondition = $this->getStateCondition($params);
        $priorityCondition = $this->getPriorityCondition($params);
        $taskListCondition = $this->getTaskListCondition($params);

        $taskBeans = $userBean
            ->withCondition("(title LIKE :title $stateCondition $priorityCondition $taskListCondition ) $orderByStatement LIMIT :limit OFFSET :offset", [
                ':title' => $this->getTitleParameter($params),
                ':limit' => $params['limit'],
                ':offset' => $params['offset']
            ])
            ->ownTaskList;

        $result = [];
        foreach($taskBeans as $taskBean)
        {
            array_push($result, $this->generateTaskArray($taskBean));
        }

        return $result;
    }

    public function getTaskById(int $userId, int $taskId)
    {
        $taskBean = RE::load('task', $taskId);
        $this->checkBeanAccess($taskBean, $userId);

        return $this->generateTaskArray($taskBean);
    }

    public function updateTask(int $userId, int $taskId, $data)
    {
        $taskBean = RE::load('task', $taskId);
        $this->checkBeanAccess($taskBean, $userId);
        $this->checkTaskCompleted($taskBean);

        if(!empty($data['title']))
        {
            $taskBean->title = $data['title'];
        }

        if(!empty($data['description']))
        {
            $taskBean->description = $data['description'];
        }

        if(!empty($data['priority']))
        {
            $taskBean->priority = $data['priority'];
        }

        if(!empty($data['dateDeadline']))
        {
            $taskBean->date_deadline = new \DateTime($data['dateDeadline']);
        }

        if(!is_null($data['state']))
        {
            $taskBean->state = $data['state'];
            
            if($data['state'] == 2)
            {
                $taskBean->date_completed = new \DateTime();
            }
        }

        $taskBean->date_changed = new \DateTime();

        R::store($taskBean);

        return $this->generateTaskArray($taskBean);
    }

    public function deleteTaskById(int $userId, int $taskId)
    {
        $taskBean = RE::load('task', $taskId);
        $this->checkBeanAccess($taskBean, $userId);
        $this->checkTaskCompleted($taskBean);

        $result = $this->generateTaskArray($taskBean);

        R::trash($taskBean);

        return $result;
    }

    public function createTask(int $userId, int $taskListId, array $data)
    {
        $taskListBean = RE::load('tasklist', $taskListId);
        $this->checkBeanAccess($taskListBean, $userId);

        $taskBean = R::dispense([
            '_type' => 'task',
            'title' => $data['title'],
            'dateCreated' => new \DateTime(),
            'dateChanged' => new \DateTime(),
            'state' => 0 // = default: open
        ]);

        if(!empty($data['description']))
        {
            $taskBean->description = $data['description'];
        }

        if(!empty($data['priority']))
        {
            $taskBean->priority = $data['priority'];
        }

        if(!empty($data['dateDeadline']))
        {
            $taskBean->date_deadline = new \DateTime($data['dateDeadline']);
        }

        $taskListBean->ownTaskList[] = $taskBean;
        R::store($taskListBean);

        $userBean = RE::load('user', $userId);
        $userBean->ownTaskList[] = $taskBean;
        R::store($userBean);

        return $this->generateTaskArray($taskBean);
    }

    private function getTitleParameter(array $params)
    {
        if(!isset($params['title']))
        {
            return "%";
        }

        return '%' . $params['title'] . '%';
    }

    private function getTaskListCondition(array $params)
    {
        if(isset($params['tasklist']))
        {
            if(Validation::id()->validate($params['tasklist'])){
                return " AND tasklist_id=" . $params['tasklist'] . " ";
            }  
        }

        return "";
    }

    private function getPriorityCondition(array $params)
    {
        if(isset($params['priority']))
        {
            if(Validation::taskPriority()->validate($params['priority']))
            {
                return " AND priority=" . $params['priority'] . " ";
            }
        }
        
        return "";
    }

    private function getStateCondition(array $params)
    {
        if(isset($params['state']))
        {
            if(Validation::taskState()->validate($params['state']))
            {
                return " AND state=". $params['state'] . " ";
            }
        }

        return "";
    }

    private function getOrderByStatement(array $params) // broken
    {
        if(isset($params['sort_by']))
        {
            $sort_order = $this->getSortOrder($params);

            if(Validation::queryParamSortByTask()->validate($params['sort_by']))
            {
                if(strcasecmp($params['sort_by'], 'tasklist') == 0)
                {
                    return " ORDER BY tasklist_id $sort_order ";
                }
                else if(strcasecmp($params['sort_by'], 'urgency') == 0)
                {
                    if(strcasecmp($sort_order, 'desc') == 0)
                    {
                        return " ORDER BY
                                    CASE WHEN state IN (0, 1) THEN 0 ELSE 1 END,
                                    CASE WHEN date_deadline IS NULL THEN 1 ELSE 0 END,
                                    date_deadline ASC,
                                    priority DESC ";
                    } 
                    else 
                    {
                        return " ORDER BY
                                    CASE WHEN state IN (0, 1) THEN 1 ELSE 0 END,
                                    CASE WHEN date_deadline IS NULL THEN 0 ELSE 1 END,
                                    date_deadline DESC,
                                    priority ASC ";
                    }
                }
                else
                {
                    return " ORDER BY " . $params['sort_by'] . " $sort_order ";
                }
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

    private function generateTaskArray($taskBean)
    {
        return [
            'id' => $taskBean->id,
            'taskListId' => $taskBean->tasklist_id,
            'title' => $taskBean->title,
            'description' => $taskBean->description,
            'priority' => $taskBean->priority,
            'dateCreated' => $taskBean->date_created,
            'dateCompleted' => $taskBean->date_completed,
            'dateDeadline' => $taskBean->date_deadline,
            'state' => $taskBean->state
        ];
    }

    private function checkBeanAccess($bean, int $userId)
    {
        if($bean->user_id != $userId)
        {
            throw new \Exception("User not authorized to access taskList", 403);
        }
    }

    private function checkTaskCompleted($taskBean)
    {
        if(date_add(new \DateTime($taskBean->date_changed), new \DateInterval('PT1M')) > new \DateTime())
        {
            return;
        }

        if(intval($taskBean->state) == 2)
        {
            throw new \Exception("Editing completed tasks is not allowed", 403);
        }
    }
}