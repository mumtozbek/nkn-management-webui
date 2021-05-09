<?php

namespace App\Console\Commands;

use App\Models\Node;
use Exception;
use Illuminate\Console\Command;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

class RestartSlowNodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restart:slow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart slow nodes.';

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
        $nodes = Node::where('uptime', '>', env('MIN_UPTIME'))->where('status', 'PERSIST_FINISHED')->whereNotNull('status')->whereRaw('(SELECT speed FROM uptimes WHERE uptimes.node_id = nodes.id ORDER BY uptimes.created_at DESC LIMIT 1) < ' . env('MIN_SPEED'))->get();

        foreach ($nodes as $node) {
            if (empty($node->account->sshKey->private_key)) {
                echo "{$node->host}: SKIPPED\n";
            } else {
                try {
                    $key = PublicKeyLoader::load($node->account->sshKey->private_key, $node->account->sshKey->password);

                    $ssh = new SSH2($node->host);
                    $ssh->setTimeout(5);
                    $ssh->login($node->account->username, $key);

                    $ssh->exec("sudo systemctl stop nkn");
                    $ssh->exec("sudo systemctl start nkn | sudo at now + 15 minutes");

                    echo "{$node->host}: RESTARTED\n";
                } catch (Exception $exception) {
                    echo "{$node->host}: FAILED (" . $exception->getMessage() . ")\n";
                }
            }
        }
    }
}
