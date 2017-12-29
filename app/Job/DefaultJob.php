<?php

namespace App\Job;
use App\Http\Controllers\Home\Common\CommonController;
use App\Model\Medal;
use App\Model\Medal_log;

/**
 * Created by PhpStorm.
 * User: tonghai
 * Date: 2017/12/13
 * Time: 13:47
 */
class DefaultJob
{
    public static function perform($json_job){
        
        echo 'hello world!';
        return true;
    }
}