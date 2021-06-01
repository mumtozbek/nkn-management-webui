<?php

namespace App\Console\Commands;

use App\Jobs\Dispatcher;
use App\Models\Node;
use Illuminate\Console\Command;

class Dispatch extends Command
{
    /**
     * Available actions.
     *
     * @var string[]
     */
    protected $actions = [
        'execute',
        'install',
        'reinstall',
        'prune',
        'start',
        'stop',
        'restart',
        'disable',
        'reboot',
        'close',
        'open',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch {action} {params?} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch node ssh commands';

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
        $action = $this->argument('action');
        $params = $this->argument('params');

        $query = Node::query();

        if ($this->option('id')) {
            $query->whereIn('id', explode(',', $this->option('id')));
        }

        $nodes = $query->get();

        if (in_array($action, $this->actions)) {
            foreach ($nodes as $node) {
                if ($action == 'execute') {
                    Dispatcher::dispatch($node, $params);
                } elseif ($action == 'install') {
                    Dispatcher::dispatch($node, [
                        "sudo mkdir -p /home/nkn/nkn-commercial/services/nkn-node",
                        "sudo echo '" . trim($node->wallet->keystore) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.json",
                        "sudo echo '" . trim($node->wallet->password) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.pswd",
                        "sudo wget -O install.sh 'http://" . env('INSTALLER_SERVER') . "/install.txt'",
                        "sudo bash install.sh > /dev/null 2>&1 &",
                    ]);
                } elseif ($action == 'reinstall') {
                    Dispatcher::dispatch($node, [
                        "sudo wget -O reinstall.sh 'http://" . env('INSTALLER_SERVER') . "/reinstall.txt'",
                        "sudo bash reinstall.sh > /dev/null 2>&1 &",
                    ]);
                } elseif ($action == 'prune') {
                    Dispatcher::dispatch($node, [
                        "sudo wget -O prune.sh 'http://" . env('INSTALLER_SERVER') . "/prune.txt'",
                        "sudo bash prune.sh > /dev/null 2>&1 &",
                    ]);
                } elseif ($action == 'start') {
                    Dispatcher::dispatch($node, [
                        "sudo systemctl start nkn",
                    ]);
                } elseif ($action == 'stop') {
                    Dispatcher::dispatch($node, [
                        "sudo systemctl stop nkn",
                    ]);
                } elseif ($action == 'restart') {
                    Dispatcher::dispatch($node, [
                        "sudo systemctl restart nkn",
                    ]);
                } elseif ($action == 'disable') {
                    Dispatcher::dispatch($node, [
                        "sudo systemctl disable nkn",
                    ]);
                } elseif ($action == 'reboot') {
                    Dispatcher::dispatch($node, [
                        "sudo reboot",
                    ]);
                } elseif ($action == 'close') {
                    Dispatcher::dispatch($node, [
                        "sudo sed -i 's/#PasswordAuthentication/PasswordAuthentication/' /etc/ssh/sshd_config",
                        "sudo sed -i 's/PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config",
                        "sudo systemctl restart ssh",
                    ]);
                } elseif ($action == 'open') {
                    Dispatcher::dispatch($node, [
                        "sudo sed -i 's/PasswordAuthentication no/PasswordAuthentication yes/' /etc/ssh/sshd_config",
                        "sudo sed -i 's/PasswordAuthentication yes/#PasswordAuthentication yes/' /etc/ssh/sshd_config",
                        "sudo systemctl restart ssh",
                    ]);
                }
            }
        } else {
            $this->error('Action "' . $action . '" not found.');
        }

        return 0;
    }
}
