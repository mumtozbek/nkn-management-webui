<?php

namespace Database\Seeders;

use App\Models\Node;
use Illuminate\Database\Seeder;

class DemoNodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 100 fake nodes
        Node::factory(100)->create();
    }
}
