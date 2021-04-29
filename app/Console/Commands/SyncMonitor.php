<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\UptimeRobot;
use Exception;
use Illuminate\Console\Command;
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
        $nodes = Node::where('status', '!=', 'OFFLINE')->get();

        $this->syncWithUptimeRobot($api, $nodes);
    }

    public function syncWithUptimeRobot($api, $nodes)
    {
        $hosts = $nodes->pluck('host')->toArray();

        try {
            $alert_contacts = collect($api->getAlertContacts())->map(function ($item) {
                return ['id' => $item['id'] . '_0_0'];
            })->pluck('id')->implode('-');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        try {
            $monitors = collect($api->getMonitors());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        $monitorsToDelete = $monitors->filter(function ($item) use ($hosts) {
            return !in_array($item['url'], $hosts);
        });

        foreach ($monitorsToDelete as $monitor) {
            try {
                $api->deleteMonitor($monitor['id']);
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        }

        $monitorsToCreate = $nodes->pluck('host')->diff($monitors->pluck('url'));

        foreach ($monitorsToCreate as $host) {
            try {
                $api->createMonitor($host, $alert_contacts);
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
    }
}
