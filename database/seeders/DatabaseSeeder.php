<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user if not exists
        if (!User::where('email', 'admin@admin.nl')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@admin.nl',
                'password' => Hash::make('test123'),
            ]);
        }

        $this->call([
            SampleDataSeeder::class,
        ]);
    }
}
