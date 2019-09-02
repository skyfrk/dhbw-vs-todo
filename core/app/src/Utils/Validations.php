<?php

namespace App\Utils;

use Respect\Validation\Validator as v;

class Validations
{
    public static function id()
    {
        return v::intVal()->positive();
    }

    public static function password()
    {
        return v::regex('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/');
    }

    public static function displayName()
    {
        return v::notEmpty()->alnum()->length(3, 20);
    }

    // Task

    public static function taskTitle()
    {
        return v::notEmpty()->alnum()->length(1,40);
    }

    public static function taskDescription()
    {
        return v::stringType()->length(1,100);
    }

    public static function taskPriority()
    {
        return v::intVal()->between(1, 5);
    }

    public static function taskDateDeadline()
    {
        return v::date();
    }

    public static function taskState()
    {
        return v::intVal()->between(0,3);
    }

    // TaskList

    public static function taskListTitle()
    {
        return v::notEmpty()->alnum()->length(1-40);
    }

    public static function taskListIcon()
    {
        return v::notEmpty()->length(1-40);
    }

    public static function taskListIsFavorite()
    {
        return v::boolVal();
    }

    // Report

    public static function reportDays()
    {
        return v::intVal()->min(1, true);
    }

    // Query parameters

    public static function queryParamLimit()
    {
        return v::intVal()->between(1, 100);
    }

    public static function queryParamOffset()
    {
        return v::intVal()->min(0);
    }

    public static function queryParamTitle()
    {
        return v::alnum()->length(1,40);
    }

    public static function queryParamSortOrder()
    {
        return v::in(['asc', 'desc']);
    }

    public static function queryParamSortByTaskList()
    {
        return v::in(['title', 'date_created']);
    }

    public static function queryParamSortByTask()
    {
        return v::in([
            'title',
            'date_created',
            'date_changed',
            'date_deadline',
            'date_completed',
            'state',
            'priority',
            'tasklist',
            'urgency'
        ]);
    }
}