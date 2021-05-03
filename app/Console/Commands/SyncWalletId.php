<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Models\Node;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

class SyncWalletId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallets:id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check wallet ids.';

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
        $wallets = Wallet::whereNull('generated_at')->get();

        foreach ($wallets as $wallet) {
            try {
                $response = Http::get("https://openapi.nkn.org/api/v1/addresses/{$wallet->address}/transactions");

                foreach ($response['data'] as $operation) {
                    if ($operation['txType'] == 'GENERATE_ID_TYPE' && $operation['fee'] == env('GENERATE_ID_FEE')) {
                        $wallet->update([
                            'generated_at' => $operation['created_at'],
                        ]);

                        break 2;
                    }
                }

                echo "{$wallet->address}: ID NOT FOUND\n";
            } catch (Exception $exception) {
                echo "{$wallet->address}: FAILED (" . $exception->getMessage() . ")\n";
            }
        }
    }
}
