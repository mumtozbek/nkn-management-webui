<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Models\Node;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
        DB::table('wallets')->update([
            'node_id' => null,
        ]);

        $nodes = Node::all();
        foreach ($nodes as $node) {
            if (empty($node->account->sshKey->private_key)) {
                echo "{$node->host}: SKIPPED\n";
            } else {
                try {
                    $key = PublicKeyLoader::load($node->account->sshKey->private_key, $node->account->sshKey->password);

                    $ssh = new SSH2($node->host);
                    $ssh->setTimeout(15);
                    $ssh->login($node->account->username, $key);

                    $keystore = json_decode($ssh->exec("cat /home/nkn/nkn-commercial/services/nkn-node/wallet.json"));
                    $password = trim($ssh->exec("cat /home/nkn/nkn-commercial/services/nkn-node/wallet.pswd"));

                    if (empty($keystore->Address)) {
                        throw new Exception("Could not fetch the wallet keystore.");
                    }

                    $wallet = Wallet::where('address', $keystore->Address)->first();
                    if ($wallet) {
                        if ($wallet->node_id != $node->id) {
                            echo "{$node->host}: DETACHED {$keystore->Address}\n";
                        }

                        $wallet->update([
                            'node_id' => $node->id,
                        ]);
                    } else {
                        if ($node->wallet) {
                            echo "{$node->host}: DETACHED {$keystore->Address}\n";

                            $node->wallet->update([
                                'node_id' => null,
                            ]);
                        }

                        Wallet::create([
                            'node_id' => $node->id,
                            'address' => $keystore->Address,
                            'keystore' => json_encode($keystore),
                            'password' => $password,
                        ]);
                    }

                    if ($ssh->isConnected()) {
                        $ssh->disconnect();
                    }

                    echo "{$node->host}: ATTACHED {$keystore->Address}\n";
                } catch (Exception $exception) {
                    echo "{$node->host}: FAILED (" . $exception->getMessage() . ")\n";
                }
            }
        }
    }
}
