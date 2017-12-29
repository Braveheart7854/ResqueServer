<?php
/**
 * Created by PhpStorm.
 * User: tonghai
 * Date: 2017/10/17
 * Time: 18:43
 */

namespace App\Service;


class RedisService
{
 
    static $instance = '';
    static $readInstance = '';

    public static function getInstance(){
        if (empty(self::$instance)){
            $redis = new \redis();
            $redis->connect(env('REDIS_HOST'),env('REDIS_PORT'));
            if (!empty(env('REDIS_PASSWORD'))){
                $redis->auth(env('REDIS_PASSWORD'));
            }
            self::$instance = $redis;
            unset($redis);
        }
        return self::$instance;
    }

    public static function getReadInstance(){
        if (empty(self::$readInstance)){
            $redis = new \redis();
            $redis->connect(env('READ_REDIS_HOST'),env('READ_REDIS_PORT'));
            if (!empty(env('READ_REDIS_PASSWORD'))){
                $redis->auth(env('READ_REDIS_PASSWORD'));
            }
            self::$readInstance = $redis;
            unset($redis);
        }
        return self::$readInstance;
    }
}