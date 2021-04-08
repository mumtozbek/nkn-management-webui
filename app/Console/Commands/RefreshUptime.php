<?php

namespace App\Console\Commands;

use App\Models\Node;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RefreshUptime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        \Log::info("Starting uptime checking...");

        Node::all()->each(function ($node) {
            $connection = @fsockopen($node->host, 30003);

            if (is_resource($connection)) {
                $response = Http::withBody('{"jsonrpc":"2.0","method":"getnodestate","params":{},"id":1}', 'application/json')->post("http://{$node->host}:30003/");

                if ($response->ok()) {
                    $json = $response->json();

                    if (!empty($json['result'])) {
                        $result = $json['result'];
                        $speed = ($result['relayMessageCount'] / $result['uptime']) * 3600;

                        $node->update([
                            'status' => $result['syncState'],
                            'version' => $result['version'],
                            'height' => $result['height'],
                            'proposals' => $result['proposalSubmitted'],
                            'relays' => $result['relayMessageCount'],
                            'uptime' => $result['uptime'],
                            'speed' => $speed,
                        ]);

                        $node->uptimes()->create([
                            'speed' => $speed,
                            'response' => json_encode($json),
                        ]);

                        return true;
                    }
                }
            }

            \Log::info("Node {$node->host} is down!");
        });

        \Log::info("Ended uptime checking.");
    }
}
