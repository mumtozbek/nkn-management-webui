<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\Shell;
use Exception;
use Illuminate\Console\Command;

class Console extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'console {commands} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shell command console';

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
        $commands = $this->argument('commands');

        $query = Node::query();

        if ($this->option('id')) {
            $query->whereIn('id', explode(',', $this->option('id')));
        }

        $nodes = $query->get();

        foreach ($nodes as $node) {
            try {
                $shell = new Shell($node);
                $result = $shell->execute($commands);

                $this->info("{$node->host}: SUCCESS" . ($result ? " ($result)" : ''));
            } catch (Exception $exception) {
                $this->error("{$node->host}: ERROR ({$exception->getMessage()})");
            }
        }
    }
}
