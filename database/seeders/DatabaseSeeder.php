<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Campaign;
use App\Models\CampaignTracking;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => Hash::make('1234'),
            'role' => UserRole::SUPER_ADMIN,
        ]);

        Artisan::call('app:import-prospects');

        Campaign::factory()->count(20)->create();

        CampaignTracking::factory()->count(200)->create();
    }
}
