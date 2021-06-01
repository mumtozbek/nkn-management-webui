<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\Models\Wallet;
use App\Shell;
use Exception;
use Illuminate\Console\Command;

class SyncWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:wallet {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync wallet information.';

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
        $query = Node::query();

        if ($this->option('id')) {
            $query->whereIn('id', explode(',', $this->option('id')));
        }

        $nodes = $query->get();

        foreach ($nodes as $node) {
            try {
                $shell = new Shell($node);
                $keystore = json_decode($shell->execute('cat /home/nkn/nkn-commercial/services/nkn-node/wallet.json'));
                $password = $shell->execute('cat /home/nkn/nkn-commercial/services/nkn-node/wallet.pswd');

                if (empty($keystore->Address)) {
                    throw new Exception("Could not fetch the wallet keystore.");
                }

                if ($node->wallet) {
                    $node->wallet->update(['node_id' => null]);

                    $this->info("{$node->host}: DETACHED {$keystore->Address}");
                }

                $wallet = Wallet::where('address', $keystore->Address)->first();
                if ($wallet) {
                    $wallet->update(['node_id' => $node->id]);
                } else {
                    Wallet::create([
                        'node_id' => $node->id,
                        'address' => $keystore->Address,
                        'keystore' => json_encode($keystore),
                        'password' => $password,
                    ]);
                }

                $this->info("{$node->host}: ATTACHED {$keystore->Address}");
            } catch (Exception $exception) {
                $this->error("{$node->host}: ERROR ({$exception->getMessage()})");
            }
        }

        return 0;
    }
}
