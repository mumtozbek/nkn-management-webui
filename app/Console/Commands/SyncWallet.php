<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use Complex\Exception;
use App\Models\Node;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

class SyncWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync wallets with nodes.';

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
        $nodes = Node::select(['nodes.*', 'wallets.address'])->leftJoin('wallets', 'wallets.node_id', '=', 'nodes.id')->whereNull('wallets.address')->get();

        foreach($nodes as $node) {
            $key = PublicKeyLoader::load($node->account->sshKey->private_key, $node->account->sshKey->password);

            $ssh = new SSH2($node->host);
            if ($ssh->login($node->account->username, $key)) {
                $keystore = json_decode($ssh->exec("cat /home/nkn/nkn-commercial/services/nkn-node/wallet.json"));
                $password = $ssh->exec("cat /home/nkn/nkn-commercial/services/nkn-node/wallet.pswd");

                $wallet = Wallet::where('address', $keystore->Address)->first();
                if ($wallet) {
                    if ($wallet->node_id != $node->id) {
                        echo $node->host . " has non-unique wallet.\n";

                        $wallet = $wallet->update([
                            'node_id' => $node->id,
                        ]);
                    }
                } else {
                    echo "Wallet $keystore->Address attached to node with ip $node->host.\n";

                    $wallet = Wallet::create([
                        'node_id' => $node->id,
                        'address' => $keystore->Address,
                        'keystore' => $keystore,
                        'password' => $password,
                    ]);
                }
            } else {
                echo "$node->host: AUTH FAILED.\n";
            }
        }

        return 0;
    }
}
