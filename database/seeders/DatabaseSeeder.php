<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SourceSeeder::class,
            PaysVilleSeeder::class,
            SpecialiteSeeder::class,
            BusinessUnitServiceSeeder::class,
            PipelineEtapeSeeder::class,
        ]);

        User::create([
            'name' => 'admin',
            'role' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password')

        ]);
    }
}
