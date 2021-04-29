<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\UptimeRobot;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync location information.';

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
        $nodes = Node::whereNull('country')->orWhereNull('region')->orWhereNull('city')->get();

        $nodes->each(function ($node) {
            try {
                $response = Http::get('http://api.ipstack.com/' . $node->host . '?access_key=' . env('IPSTACK_ACCESS_KEY') . '&format=1');

                $node->update([
                    'country' => $response['country_name'],
                    'region' => $response['region_name'],
                    'city' => $response['city'],
                ]);
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        });
    }
}
