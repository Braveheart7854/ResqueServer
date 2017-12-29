<?php
/**
 * Created by PhpStorm.
 * User: tonghai
 * Date: 2017/12/11
 * Time: 19:10
 */

namespace App\Service;


use Mockery\CountValidator\Exception;

class ConsumeService
{
    static private $QUEUE = 'default';
    static private $BLOCKING;
    static private $resqueInstance = '';
    static public $consumeService = '';
    static private $job;
    static private $mainProcess = [];

    public static function init($count,$QUEUE,$BLOCKING){
        try{
            set_time_limit(0);
            ini_set('default_socket_timeout', -1); //队列处理不超时,解决redis报错:read error on connection
            self::$QUEUE = $QUEUE;
            self::$BLOCKING = $BLOCKING;
            self::$resqueInstance = new ResqueService($QUEUE);
            self::$consumeService = new self;

            if (!isset(config('resqueQueue')[self::$QUEUE]))
                die('Not found the job of this queue!');
            self::$job = config('resqueQueue')[self::$QUEUE];

            if($count > 1) {
                for($i = 0; $i < $count; ++$i) {
                    $process = new \swoole_process([self::$consumeService,'createMainProcess']);
                    $pid = $process->start();
                    if ($pid === false)
                        die('Could not fork worker {count}');
                    self::$mainProcess[$pid] = $process;
                }

                foreach(self::$mainProcess as $id=>$mainPro){
                    echo $mainPro->read();
                }

            }
            // Start a single worker
            else {
                $worker = self::$resqueInstance;
                $interval = 5;
                $worker->work($interval, $BLOCKING);
            }
        }catch (Exception $e){
            ResqueService::resqueLog($e->getMessage()."\r\n".json_encode($e->getTrace()));
        }
    }

    public static function createMainProcess(\swoole_process $worker){
        $worker->write(date('Y-m-d H:i:s',time()).": Workers has started!\r\n");

        $workerResque = self::$resqueInstance;
        $interval = 5;
        $workerResque->work($interval, self::$BLOCKING);
    }

    public static function process(\swoole_process $worker){// 第一个处理
        $GLOBALS['worker'] = $worker;
        swoole_event_add($worker->pipe, function($pipe) {
            $worker = $GLOBALS['worker'];
            $recv = $worker->read();            //receive data from master

            try{

                $jobServ = self::$job;
                if ($jobServ::perform($recv) === false)
                    self::$resqueInstance->rollbackToQueue($recv);

                $worker->write('perfect');

            }catch (\Exception $e){
                self::$resqueInstance->rollbackToQueue($recv);
                ResqueService::resqueLog($e->getMessage()."\r\n".json_encode($e->getTrace()));
                $worker->write('shutdown');
            }
            $worker->exit(0);
        });
        exit;
    }

}