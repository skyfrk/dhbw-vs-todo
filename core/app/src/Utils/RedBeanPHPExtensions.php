<?php

namespace App\Utils;

use RedBeanPHP\R as R;

class RedBeanPHPExtensions
{
    public static function load(string $type, int $id)
    {
        $bean = R::load($type, $id);
        
        if($bean->id != $id)
        {
            throw new \Exception("Bean of type " . $type . " not found", 404);
        }

        return $bean;
    }
}