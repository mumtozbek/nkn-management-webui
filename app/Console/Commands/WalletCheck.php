<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\Models\Wallet;
use App\Shell;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WalletCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:check {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check wallet id information.';

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
        $query = Wallet::query();

        if ($this->option('id')) {
            $query->whereIn('id', explode(',', $this->option('id')));
        }

        $wallets = $query->get();

        foreach ($wallets as $wallet) {
            try {
                $response = Http::get("https://openapi.nkn.org/api/v1/addresses/{$wallet->address}/transactions");

                foreach ($response['data'] as $operation) {
                    if ($operation['txType'] == 'GENERATE_ID_TYPE' && $operation['block_id'] >= env('GENERATE_ID_START')) {
                        $wallet->update(['generated_at' => $operation['created_at']]);

                        continue 2;
                    }
                }

                $this->info("{$wallet->address}: ERROR (ID NOT FOUND)");
            } catch (Exception $exception) {
                $this->error("{$wallet->address}: ERROR ({$exception->getMessage()})");
            }
        }

        return 0;
    }
}
