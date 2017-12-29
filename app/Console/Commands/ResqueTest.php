<?php

namespace App\Console\Commands;

use App\Service\ResqueServiceTest;
use Illuminate\Console\Command;

class ResqueTest extends Command
{
    /**
     * 消费进程
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resqueTest:run {count=1} {queue=default} {blocking=false}';

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
        $count = 2;
        $COUNT = $_SERVER['argv'][2] ?? 2;
        if(!empty($COUNT) && $COUNT > 1) {
            $count = $COUNT;
        }
        $QUEUE = $_SERVER['argv'][3] ?? 'pointsMQS#jzn';

        $BLOCKING = $_SERVER['argv'][4] ?? false;

        if($count > 1) {
            for($i = 0; $i < $count; ++$i) {
                $pid = ResqueServiceTest::fork();
                if($pid === false || $pid === -1) {
                    die('Could not fork worker {count}');
                }
                // Child, start the worker
                else if(!$pid) {
                    $worker = new ResqueServiceTest($QUEUE);
                    $interval = 5;
                    $worker->work($interval, $BLOCKING);
//                    break;
                }
            }
        }
// Start a single worker
        else {
            $worker = new ResqueServiceTest($QUEUE);
            $interval = 5;
            $worker->work($interval, $BLOCKING);
        }
    }
}
