<?php

namespace App\Services;

use Interop\Container\ContainerInterface;
use RedBeanPHP\R as R;
use App\Utils\RedBeanPHPExtensions as RE;

class ReportService
{
    public function getReport(string $userId, int $days)
    {
        $userBean = RE::load('user', $userId);

        return $this->createReport($userBean, $days);
    }

    public function getTaskListReport($userId, $taskListId, $days)
    {
        $taskListBean = RE::load('tasklist', $taskListId);
        $this->checkBeanAccess($taskListBean, $userId);

        return $this->createReport($taskListBean, $days);
    }

    private function createReport($bean, $days)
    {
        $reportStartDate = date("Y-m-d H:i:s", date_timestamp_get(date_sub(new \DateTime(), new \DateInterval('P'.$days.'D'))));

        $result = [
            'openTaskCount' => 0,
            'openOverdueTaskCount' => 0,
            'inProgressTaskCount' => 0,
            'cancelledTaskCount' => 0,
            'finishedTaskCount' => 0,
            'finishedOverdueTaskCount' => 0,
            'createdTaskCount' => 0,
            'urgentTasks' => []
        ];

        $absoluteTaskInfo = $this->getAbsoluteTaskInfo($bean);
        $result['openTaskCount'] = $absoluteTaskInfo['openTaskCount'];
        $result['openOverdueTaskCount'] = $absoluteTaskInfo['openOverdueTaskCount'];
        $result['inProgressTaskCount'] = $absoluteTaskInfo['inProgressTaskCount'];

        $periodRelatedTaskInfo = $this->getPeriodRelatedTaskInfo($bean, $reportStartDate);
        $result['cancelledTaskCount'] = $periodRelatedTaskInfo['cancelledTaskCount'];
        $result['finishedTaskCount'] = $periodRelatedTaskInfo['finishedTaskCount'];
        $result['finishedOverdueTaskCount'] = $periodRelatedTaskInfo['finishedOverdueTaskCount'];

        $result['createdTaskCount'] = $this->getCountOfCreatedTasksInPeriod($bean, $reportStartDate);

        $result['urgentTasks'] = $this->getUrgentTasks($bean, $reportStartDate)['urgentTasks'];

        return $result;
    }

    private function getUrgentTasks($bean, $reportStartDate)
    {
        $result = ['urgentTasks' => []];

        $taskBeansInPeriodSortedByPriority = $bean
        ->withCondition(' ( date_changed > :date AND state IN ( 0, 1 ) ) ORDER BY CASE WHEN date_deadline IS NULL THEN 1 ELSE 0 END, date_deadline ASC, priority DESC ', ['date' => $reportStartDate])
        ->ownTaskList;

        $urgentTaskBeans = array_slice($taskBeansInPeriodSortedByPriority, 0 , 5);

        foreach($urgentTaskBeans as $taskBean)
        {
            array_push($result['urgentTasks'], $this->generateTaskArray($taskBean)); 
        }

        return $result;
    }

    private function getCountOfCreatedTasksInPeriod($bean, $reportStartDate)
    {
        $taskBeansInPeriodCreated = $bean
        ->withCondition(' date_created > :date ', ['date' => $reportStartDate])
        ->ownTaskList;

        return count($taskBeansInPeriodCreated);
    }

    private function getPeriodRelatedTaskInfo($bean, $reportStartDate)
    {
        $result = [
            'cancelledTaskCount' => 0,
            'finishedTaskCount' => 0,
            'finishedOverdueTaskCount' => 0
        ];

        $taskBeansInPeriod = $bean
            ->withCondition(' date_changed > :date ', ['date' => $reportStartDate])
            ->ownTaskList;

        foreach($taskBeansInPeriod as $taskBean)
        {
            if($taskBean->state == 3)
            {
                $result['cancelledTaskCount']++;
            }
            else if($taskBean->state == 2)
            {
                $result['finishedTaskCount']++;

                if(new \DateTime($taskBean->date_deadline) < new \DateTime($taskBean->date_changed)){
                    $result['finishedOverdueTaskCount']++;
                }
            }
        }

        return $result;
    }

    private function getAbsoluteTaskInfo($bean)
    {
        $allTaskBeans = $bean->ownTaskList;

        $result = [
            'openTaskCount' => 0,
            'openOverdueTaskCount' => 0,
            'inProgressTaskCount' => 0
        ];

        foreach($allTaskBeans as $taskBean)
        {
            if($taskBean->state == 0)
            {
                $result['openTaskCount']++;

                if(new \DateTime($taskBean->date_deadline) < new \DateTime()){
                    $result['openOverdueTaskCount']++;
                }
            } 
            else if($taskBean->state == 1)
            {
                $result['inProgressTaskCount']++;
            }
        }

        return $result;
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

    private function checkBeanAccess($bean, $userId)
    {
        if($bean->user_id != $userId)
        {
            throw new \Exception("User not authorized to access taskList", 403);
        }
    }
}
