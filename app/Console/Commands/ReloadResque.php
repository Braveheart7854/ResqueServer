<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReloadResque extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resque:reload {queueName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command for reloading the resque';

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
            $queueName = $_SERVER['argv'][2] ?? '';
            if ($queueName === '') {
                echo 'Params queueName can not be null!';
                exit;
            }

            $this->killProcess('resque : Waiting for '.$queueName);
            $this->killProcess('resque : Paused '.$queueName);
            $this->killProcess('resque : Processing '.$queueName);

            echo "waiting...\r\n";

            $path = __DIR__ . '/../../../';
            $start_master_cmd = "php $path/artisan resque:run ".$queueName." 2 >> $path/storage/logs/start_resque.log &";
            shell_exec($start_master_cmd);
            echo date('Y-m-d H:i:s',time()).": Workers has restarted!\r\n";
        }catch (\Exception $e){
            echo date('Y-m-d H:i:s',time())." : error \r\n". $e->getMessage()."\r\n".$e->getTrace()."\r\n";
        }
    }

    private function killProcess($process){
        exec("ps x|grep '".$process."'|grep -v grep|awk '{print $1}'", $output, $ret);

        if (!empty($output)){
            foreach($output as $item){
                if (posix_kill(intval($item),SIGQUIT))
                    echo "PID ".intval($item)." stopped\r\n";
            }
        }
    }
}
