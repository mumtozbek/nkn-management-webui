<?php

namespace App;

use App\Models\Node;
use Exception;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

class Shell
{
    /**
     * @var Node
     */
    private $node;

    /**
     * @var int
     */
    private $timeout;

    /**
     * Create a new shell instance.
     *
     * @return void
     */
    public function __construct(Node $node, int $timeout = 15)
    {
        $this->node = $node;
        $this->timeout = $timeout;
    }

    /**
     * Execute the shell command.
     *
     * @return mixed
     */
    public function execute($command)
    {
        try {
            $key = PublicKeyLoader::load($this->node->account->sshKey->private_key, $this->node->account->sshKey->password);

            $ssh = new SSH2($this->node->host);
            $ssh->setTimeout($this->timeout);
            $ssh->login($this->node->account->username, $key);

            $result = $ssh->exec($command);
            $result = trim($result, "\n");

            $ssh->disconnect();
        } catch (Exception $exception) {
            throw $exception;
        }

        return $result;
    }
}
