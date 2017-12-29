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
        for ($i =1; $i<=1000;$i++){
            $account = 15068775512;
            $area = 8;
            $time = rand(1508392000,time());
            $job = ['event_id'=> 7, 'account'=> $account,'area'=> $area, 'timestamp'=>$time, 'points'=>1];
            $job = json_encode($job);

            $redisServ->lPush('pointsMQS#jzn',$job);
        }

    }
}
