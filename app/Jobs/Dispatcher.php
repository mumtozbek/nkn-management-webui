<?php

namespace App\Jobs;

use App\Models\Node;
use App\Shell;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Dispatcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $node;
    private $query;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Node $node, $query)
    {
        $this->node = $node;
        $this->query = $query;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $shell = new Shell($this->node);

            $commands = [];
            if (is_string($this->query)) {
                $commands[] = $this->query;
            } elseif (is_array($this->query)) {
                foreach ($this->query as $command) {
                    $commands[] = $command;
                }
            }

            foreach ($commands as $command) {
                $result = $shell->execute($command);

                if (config('app.debug')) {
                    Log::channel('queue')->debug("JOB {$this->job->getJobId()}, HOST {$this->node->host} returned: \"$result\"");
                }
            }
        } catch (Exception $exception) {
            $this->fail($exception);
        }
    }
}
