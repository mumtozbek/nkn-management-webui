<?php

namespace App\Console\Commands;

use App\Models\Node;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncUptime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:uptime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync uptime information.';

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
        $nodes = Node::all();

        // Check all nodes' states
        $nodes->each(function ($node) {
            try {
                $data = [
                    'jsonrpc' => '2.0',
                    'id' => '1',
                    'method' => 'getnodestate',
                    'params' => (object)[],
                ];

                $ch = curl_init("http://{$node->host}:30003/");
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, env('CURLOPT_CONNECTTIMEOUT', 3));
                curl_setopt($ch, CURLOPT_TIMEOUT, env('CURLOPT_TIMEOUT', 3));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen(json_encode($data))
                    ]
                );

                $response = curl_exec($ch);
                $info = curl_getinfo($ch);

                curl_close($ch);

                if (is_string($response)) {
                    $json = json_decode($response);

                    if (!empty($json)) {
                        if (!empty($json->result)) {
                            $node->index($json, $info);

                            return true;
                        } elseif (!empty($json->error)) {
                            if ($json->error->code == '-45022') {
                                $status = 'GENERATE_ID';
                            } elseif ($json->error->code == '-45024') {
                                $status = 'PRUNING_DB';
                            } else {
                                Log::channel('daily')->alert("Node {$node->host} returned error:" . json_encode($json->error));

                                return true;
                            }

                            $node->update([
                                'status' => $status,
                            ]);

                            return true;
                        }
                    }
                }

                if ($node->status != null) {
                    if ($node->status != 'TIMEOUT') {
                        Log::channel('daily')->alert("Node {$node->host} is down!");

                        mail(env('MAIL_ADMIN'), "Node {$node->host} is down!", "Node {$node->host} is down!", '', '-f' . env('MAIL_FROM_ADDRESS'));
                    }

                    // Connection failed, so log it
                    $node->update([
                        'status' => 'TIMEOUT',
                    ]);
                }
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        });
    }
}
