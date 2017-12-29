<?php

namespace App\Console\Commands;

use App\Service\ConsumeService;
use App\Service\ResqueService;
use Illuminate\Console\Command;

class Resque extends Command
{
    /**
     * 消费进程
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resque:run {queue=default} {count=1} {blocking=false}';

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

        $count = 1;
        $COUNT = $_SERVER['argv'][3] ?? 1;
        if(!empty($COUNT) && $COUNT > 1) {
            $count = $COUNT;
        }

        $BLOCKING = $_SERVER['argv'][4] ?? false;

        ConsumeService::init($count,$QUEUE,$BLOCKING);
    }
}
