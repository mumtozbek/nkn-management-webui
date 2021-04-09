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
            $response = $this->getNodeState($node->host);

            // Connection success
            if (is_string($response)) {
                $json = json_decode($response);

                if (!empty($json)) {
                    if (!empty($json->result)) {
                        $result = $json->result;
                        $speed = ($result->relayMessageCount / $result->uptime) * 3600;

                        $node->update([
                            'status' => $result->syncState,
                            'version' => $result->version,
                            'height' => $result->height,
                            'proposals' => $result->proposalSubmitted,
                            'relays' => $result->relayMessageCount,
                            'uptime' => $result->uptime,
                            'speed' => $speed,
                        ]);

                        $node->uptimes()->create([
                            'speed' => $speed,
                            'response' => json_encode($json),
                        ]);

                        return true;
                    } elseif (!empty($json->error)) {
                        if ($json->error->code == '-45022') {
                            $status = 'GENERATE_ID';
                        } else {
                            $status = $json->error->code;
                        }

                        $node->update([
                            'status' => $status,
                            'speed' => null,
                        ]);

                        return true;
                    }
                }
            }

            // Connection failed, so log it
            $node->update([
                'status' => 'OFFLINE',
            ]);

            \Log::info("Node {$node->host} is down!");
        });

        \Log::info("Ended uptime checking.");
    }

    private function getNodeState($host)
    {
        $data_string = '{"jsonrpc":"2.0","method":"getnodestate","params":{},"id":1}';

        $ch = curl_init('http://' . $host . ':30003/');

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
            ]
        );

        return curl_exec($ch);
    }
}
