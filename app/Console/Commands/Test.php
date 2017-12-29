<?php

namespace App\Console\Commands;

use App\Job\JznJob;
use App\Model\Medal;
use App\Service\ResqueServiceTest;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * 消费进程
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:run {count=1} {queue=default} {blocking=false}';

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
        try{

            echo date('Y-m-d H:i:s',time())."\r\n";

            while (true){
                \DB::table('medal')->where(['id'=>1])->first();
            }

        }catch (\Exception $e){
            echo date('Y-m-d H:i:s',time())."\r\n";
            echo $e->getMessage();
            exit;
        }
//        \DB::reconnect();

//        $a = microtime();
//        echo microtime()."\r\n";
//        $job = ['event_id'=>7, 'account'=> '4768','area'=> 9, 'timestamp'=>1508392993, 'points'=>1];
//        $job = json_encode($job);
//        JznJob::perform($job);
//        $b = microtime();
//        echo microtime()."\r\n";
//        echo $b-$a."\r\n";
    }
}
