<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Node;
use Illuminate\Database\Seeder;

class NodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 100 fake nodes
        Node::factory(100)->create(['account_id' => Account::first()->id]);
    }
}
