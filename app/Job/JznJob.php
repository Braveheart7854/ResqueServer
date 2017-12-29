<?php

namespace App\Job;
use App\Http\Controllers\Home\Common\CommonController;
use App\Model\Medal;
use App\Model\Medal_log;
use App\Model\MedalLianyun;
use App\Model\MedalLianyunLog;

/**
 * Created by PhpStorm.
 * User: tonghai
 * Date: 2017/12/13
 * Time: 13:47
 */
class JznJob
{
    const USERINFO_API = 'http://client.ip.sanguosha.com/game_user.sgs';

    public static function perform($json_job){
//        $job = ['event_id'=> 1, 'account'=> '4768','area'=> 9, 'timestamp'=>1508392993, 'points'=>1];
//        $job = json_encode($job);

        $job = json_decode($json_job);
        if ($job->event_id != Medal::EVENT_MEDAL) return false;

//        $url = self::USERINFO_API.'?areaid='.$job->area.'&account='.$job->account;
//        $info = CommonController::http($url,[]);
//        $nick = $info['Name'] ?? '';
        $nick = '';

        if ($job->area == 10){
            if (!MedalLianyun::insertMedal($job,$nick)) return false;
            @MedalLianyunLog::insertLog($job);
        }else{
            if (!Medal::insertMedal($job,$nick)) return false;
            @Medal_log::insertLog($job);
        }

        return true;
    }
}