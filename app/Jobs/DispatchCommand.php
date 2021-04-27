<?php

namespace App\Jobs;

use App\Models\Node;
use Complex\Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

class DispatchCommand implements ShouldQueue
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
        $key = PublicKeyLoader::load($this->node->account->sshKey->private_key, $this->node->account->sshKey->password);

        $ssh = new SSH2($this->node->host);
        if (!$ssh->login($this->node->account->username, $key)) {
            $this->fail(new Exception("{$this->node->host}: AUTH FAILED."));
        }

        if ($this->node->account->password) {
            $result = $ssh->exec("echo {$this->node->account->password} | sudo -S " . $this->query);
        } else {
            $result = $ssh->exec("sudo -S {$this->query}");
        }

        Log::info("{$this->node->host}: " . $result . '.');
    }
}
