<?php

namespace App\Console\Commands;

use App\Service\ResqueServiceNew;
use Illuminate\Console\Command;

class ResqueNew extends Command
{
    /**
     * 消费进程(性能较Resque.php有所提升)
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resqueNew:run {queue=default} {count=1} {blocking=false}';

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
        $QUEUE = $_SERVER['argv'][2] ?? 'default';

        $count = 2;
        $COUNT = $_SERVER['argv'][3] ?? 2;
        if(!empty($COUNT) && $COUNT > 1) {
            $count = $COUNT;
        }

        $BLOCKING = $_SERVER['argv'][4] ?? false;

        if($count > 1) {
            for($i = 0; $i < $count; ++$i) {
                $pid = ResqueServiceNew::fork();
                if($pid === false || $pid === -1) {
                    die('Could not fork worker {count}');
                }
                // Child, start the worker
                else if(!$pid) {
                    $worker = new ResqueServiceNew($QUEUE);
                    $interval = 5;
                    $worker->work($interval, $BLOCKING);
//                    break;
                }
            }
        }
// Start a single worker
        else {
            $worker = new ResqueServiceNew($QUEUE);
            $interval = 5;
            $worker->work($interval, $BLOCKING);
        }
    }
}
