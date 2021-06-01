<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create demo user
        User::create(['email' => 'test@example.com', 'name' => 'John Doe']);

        // Create demo provider
        $provider = Provider::create(['name' => 'Test']);

        // Create demo account
        Account::create(['name' => 'Test', 'provider_id' => $provider->id]);
    }
}
