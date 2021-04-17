<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\UptimeRobot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync monitoring information.';

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

        $this->syncWithUptimeRobot($api, $nodes);
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
