<?php
/**
 * Created by PhpStorm.
 * User: tonghai
 * Date: 2017/10/20
 * Time: 9:32
 */

namespace App\Service;

class ResqueServiceNew
{

    public $shutdown = false;
    public $paused = false;
    public $queue;  //任务队列
    public $queueError;  //无法处理的任务队列
    public $keyError;  //无法处理的任务计数key
    public $redis;
    public $child;
    const  DEFAULT_INTERVAL = 5;
    static private $job;
    static private $resqueInstance = '';

    public function __construct($queue = 'default')
    {
        $this->queue = $queue;
        $this->queueError = $queue.'_error';
        $this->keyError = $queue.'_error_count';

        $this->redis = RedisService::getReadInstance();
        self::$resqueInstance = new ResqueService($this->queue);

        if (!isset(config('resqueQueue')[$this->queue]))
            die('Not found the job of this queue!');
        self::$job = config('resqueQueue')[$this->queue];
    }

    public static function fork()
    {
        if(!function_exists('pcntl_fork')) {
            return false;
        }

        // Close the connection to Redis before forking.
        // This is a workaround for issues phpredis has.
//        self::$redis = null;

        $pid = pcntl_fork();
        if($pid === -1) {
            throw new \RuntimeException('Unable to fork child worker.');
        }

        return $pid;
    }

    public function work($interval = self::DEFAULT_INTERVAL, $blocking = false)
    {
        echo date('Y-m-d H:i:s',time()).": Workers has started!\r\n";
//        $this->updateProcLine('Starting');
        $this->startup();

        while(true) {
            $this->patchSignal();

            if($this->shutdown) {
                break;
            }

            // Attempt to find and reserve a job
            $job = false;
            if(!$this->paused) {
                $this->updateProcLine('Waiting for ' .$this->queue);

                $job = $this->reserve($blocking, $interval);
            }

            if(!$job) {

                if($blocking === false)
                {
                    // If no job was found, we sleep for $interval before continuing and checking again
                    if($this->paused) {
                        $this->updateProcLine('Paused ' .  $this->queue);
                    }
                    else {
                        $this->updateProcLine('Waiting for ' .  $this->queue);
                    }
                    usleep($interval * 1000000);
                }
                continue;
            }
            $status = 'Processing ' . $this->queue;
            $this->updateProcLine($status);
            $this->perform($job);
        }

    }

    private function startup()
    {
        $this->registerSigHandlers();
    }

    private function registerSigHandlers(){
        if(!function_exists('pcntl_signal')) {
            return;
        }

        pcntl_signal(SIGTERM, array($this, 'shutDownNow'));
        pcntl_signal(SIGINT, array($this, 'shutDownNow'));
        pcntl_signal(SIGQUIT, array($this, 'shutdown'));
//        pcntl_signal(SIGUSR1, array($this, 'killChild'));
        pcntl_signal(SIGUSR2, array($this, 'pauseProcessing'));
        pcntl_signal(SIGCONT, array($this, 'unPauseProcessing'));
    }

    private function patchSignal(){
        if(!function_exists('pcntl_signal_dispatch')) {
            return;
        }
        pcntl_signal_dispatch();
    }

    /**
     * Signal handler callback for USR2, pauses processing of new jobs.
     */
    public function pauseProcessing()
    {
        $this->paused = true;
    }

    /**
     * Signal handler callback for CONT, resumes worker allowing it to pick
     * up new jobs.
     */
    public function unPauseProcessing()
    {
        $this->paused = false;
    }

    /**
     * Schedule a worker for shutdown. Will finish processing the current job
     * and when the timeout interval is reached, the worker will shut down.
     */
    public function shutdown()
    {
        $this->shutdown = true;
    }

    /**
     * Force an immediate shutdown of the worker, killing any child jobs
     * currently running.
     */
    public function shutdownNow()
    {
        $this->shutdown();
    }

    private function updateProcLine($status)
    {
        $processTitle = 'resque : ' . $status;
        if(function_exists('cli_set_process_title') && PHP_OS !== 'Darwin') {
            cli_set_process_title($processTitle);
        }
        else if(function_exists('setproctitle')) {
            setproctitle($processTitle);
        }
    }

    public function reserve($blocking = false, $timeout = null)
    {
        if($blocking === true) {
            $job = $this->redis->blPop($this->queue,$timeout);
            if($job) return $job;
        } else {
            $job = $this->redis->lPop($this->queue);
            if($job) return $job;
        }
        return false;
    }

    public function perform($json_job){

        try{

            $jobServ = self::$job;
            if ($jobServ::perform($json_job) === false)
                self::$resqueInstance->rollbackToQueue($json_job);

        }catch (\Exception $e){
            self::$resqueInstance->rollbackToQueue($json_job);
            ResqueService::resqueLog($e->getMessage()."\r\n".json_encode($e->getTrace()));
        }
    }

    public function resqueLog($msg){
        if (!file_exists(__DIR__.'/../../storage/logs'))
            mkdir(__DIR__.'/../../storage/logs');
        file_put_contents(__DIR__.'/../../storage/logs/error_resque.log',date('Y-m-d H:i:s',time())." Error:\r\n".$msg."\r\n\r\n",FILE_APPEND);
    }
}