<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\UptimeRobot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
    public function handle(UptimeRobot $api)
    {
        $nodes = Node::all();

        // Keep up with uptimerobot.com
        $this->syncWithUptimeRobot($api, $nodes);

        // Check all nodes' states
        $nodes->each(function ($node) {
            $mined = false;

            $response = $this->getNodeState($node->host);
            if (is_string($response)) {
                $json = json_decode($response);

                if (!empty($json)) {
                    if (!empty($json->result)) {
                        $result = $json->result;
                        $speed = ($result->relayMessageCount / $result->uptime) * 3600;
                        $blocks = (int)$result->height - (int)$node->height;

                        if ($result->proposalSubmitted > $node->proposals) {
                            $mined = true;
                        }

                        $node->update([
                            'status' => $result->syncState,
                            'version' => $result->version,
                            'height' => $result->height,
                            'proposals' => $result->proposalSubmitted,
                            'relays' => $result->relayMessageCount,
                            'uptime' => $result->uptime,
                        ]);

                        if ($result->syncState != 'PERSIST_FINISHED') {
                            $node->blocks()->create([
                                'count' => $blocks,
                            ]);
                        }

                        $node->uptimes()->create([
                            'speed' => $speed,
                            'response' => json_encode($json),
                        ]);

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

                        if ($status == $json->error->code) {
                            $node->uptimes()->create([
                                'speed' => 0,
                            ]);
                        }

                        return true;
                    }
                }
            }

            // Connection failed, so log it
            $node->update([
                'status' => 'OFFLINE',
            ]);

            Log::channel('daily')->alert("Node {$node->host} is down!");

            mail(env('MAIL_ADMIN'), "Node {$node->host} is down!", "Node {$node->host} is down!", '', '-f' . env('MAIL_FROM_ADDRESS'));

            if ($mined) {
                mail(env('MAIL_ADMIN'), "Node {$node->host} is just mined!", "Node {$node->host} is just mined!", '', '-f' . env('MAIL_FROM_ADDRESS'));
            }
        });
    }

    private function getNodeState($host)
    {
        $data = [
            'jsonrpc' => '2.0',
            'id' => '1',
            'method' => 'getnodestate',
            'params' => (object)[],
        ];

        $ch = curl_init('http://' . $host . ':30003/');

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            ]
        );

        return curl_exec($ch);
    }

    public function syncWithUptimeRobot($api, $nodes)
    {
        $alert_contacts = collect($api->getAlertContacts())->map(function ($item) {
            return ['id' => $item['id'] . '_0_0'];
        })->pluck('id')->implode('-');

        $hosts = $nodes->pluck('host')->toArray();
        $monitors = collect($api->getMonitors());

        $monitorsToDelete = $monitors->filter(function ($item) use ($hosts) {
            return !in_array($item['url'], $hosts);
        });

        foreach ($monitorsToDelete as $monitor) {
            $api->deleteMonitor($monitor['id']);
        }

        $monitorsToCreate = $nodes->pluck('host')->diff($monitors->pluck('url'));

        foreach ($monitorsToCreate as $host) {
            $api->createMonitor($host, $alert_contacts);
        }
    }
}
