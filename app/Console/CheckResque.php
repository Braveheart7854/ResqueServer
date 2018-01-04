<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckResque
{
    /**
     * 计划任务 定时执行  半小时执行一次
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resque:check {queueName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command for checking the process of resque which is existed, otherwise reload it';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
//        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{

            $queueName = $_SERVER['argv'][1] ?? '';
            if ($queueName === '') {
                echo 'Params queueName can not be null!';
                exit;
            }

            $cmdWait = "ps axu|grep \"resque : Waiting for $queueName\"|grep -v \"grep\"|wc -l";
            $retWait = shell_exec("$cmdWait");
            $resWait = rtrim($retWait, "\r\n");

            $cmdPaused = 'ps axu|grep "resque : Paused '.$queueName.'"|grep -v "grep"|wc -l';
            $retPaused = shell_exec("$cmdPaused");
            $resPaused = rtrim($retPaused, "\r\n");

            $cmdProcess = 'ps axu|grep "resque : Processing '.$queueName.'"|grep -v "grep"|wc -l';
            $retProcess = shell_exec("$cmdProcess");
            $resProcess = rtrim($retProcess, "\r\n");

            if($resWait === "0" && $resPaused === "0" && $resProcess === "0") {
                if (!file_exists(__DIR__ . '/../../storage/logs'))
                    mkdir(__DIR__ . '/../../storage/logs');
                $path = __DIR__ . '/../../';
                $start_master_cmd = "php $path/artisan resque:run ".$queueName." 2 >> $path/storage/logs/start_resque.log &";
                shell_exec("$start_master_cmd");
                echo date('Y-m-d H:i:s',time())." : process restarted\r\n";
            }
            echo date('Y-m-d H:i:s',time())." : process has already started\r\n";
        }catch(\Exception $e){
            echo date('Y-m-d H:i:s',time())." : error \r\n". $e->getMessage()."\r\n".$e->getTrace()."\r\n";
        }
    }
}

$check = new CheckResque();
$check->handle();