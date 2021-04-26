<?php

namespace App\Console\Commands;

use App\Jobs\DispatchCommand;
use App\Models\Node;
use Illuminate\Console\Command;

class Console extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'console:dispatch {query}';

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
            if (!empty($node->account->username) && !empty($node->account->sshKey)) {
                DispatchCommand::dispatch($node, $query);
            }
        }

        return 0;
    }
}
