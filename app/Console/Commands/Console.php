<?php

namespace App\Console\Commands;

use App\Models\Node;
use Illuminate\Console\Command;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

class Console extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'console:execute {query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute command on nodes.';

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
        $query = $this->argument('query');

        $nodes = Node::all();
        foreach ($nodes as $node) {
            if (empty($node->account->username) || empty($node->account->sshKey)) {
                echo "$node->host: SKIPPED\n";
            } else {
                $key = PublicKeyLoader::load($node->account->sshKey->private_key, $node->account->sshKey->password);

                $ssh = new SSH2($node->host);
                if (!$ssh->login($node->account->username, $key)) {
                    echo "$node->host: AUTH FAILED\n";
                }

                echo "$node->host: " . $ssh->exec($query);
            }
        }

        return 0;
    }
}
