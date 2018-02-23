<?php

namespace App\Console\Commands;

use App\Service\RedisService;
use App\Service\ResqueService;
use Illuminate\Console\Command;

class InsertData extends Command
{
    /**
     * 消费进程
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insertData:run {count=1} {queue=default} {blocking=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'resque process which consume queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $redisServ = RedisService::getReadInstance();
        for ($i =1; $i<=100000;$i++){
            if ($i > 80000){
                $area = 9;
            }elseif ($i > 60000){
                $area = 8;
            }elseif ($i > 40000){
                $area = 5;
            }elseif ($i > 20000){
                $area = 2;
            }else{
                $area = 1;
            }
            $account = '15068775512';
            $time = rand(1508392000,time());
            $job = ['event_id'=> 7, 'account'=> $account,'area'=> $area, 'timestamp'=>$time, 'points'=>1];
            $job = json_encode($job);

            $redisServ->lPush('pointsMQS#dns',$job);
        }
    }
}
