<?php

namespace App\Console\Commands;

use App\Models\Node;
use Exception;
use Illuminate\Console\Command;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

class RebootNodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restart:reboot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reboot nodes.';

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
     * @return int
     */
    public function handle()
    {
        $maxUptime = max(86400, env('MAX_UPTIME'));

        $nodes = Node::where('uptime', '>=', $maxUptime)->where('status', 'PERSIST_FINISHED')->whereNotNull('status')->get();

        foreach ($nodes as $node) {
            if (empty($node->account->sshKey->private_key)) {
                echo "{$node->host}: SKIPPED\n";
            } else {
                try {
                    $key = PublicKeyLoader::load($node->account->sshKey->private_key, $node->account->sshKey->password);

                    $ssh = new SSH2($node->host);
                    $ssh->setTimeout(5);
                    $ssh->login($node->account->username, $key);

                    $ssh->exec("sudo systemctl stop nkn-commercial");
                    $ssh->exec("sudo reboot");

                    echo "{$node->host}: REBOOTED\n";
                } catch (Exception $exception) {
                    echo "{$node->host}: FAILED (" . $exception->getMessage() . ")\n";
                }
            }
        }
    }
}
