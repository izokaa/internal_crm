<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pays;
use App\Models\Specialite;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Pays::create(['nom' => 'Maroc']);
        Pays::create(['nom' => 'France']);
        Pays::create(['nom' => 'Allemagne']);
        Pays::create(['nom' => 'Espagne']);

        Specialite::create(['nom' => 'Médecin Généraliste']);
        Specialite::create(['nom' => 'Développeur Web']);
        Specialite::create(['nom' => 'Consultant Marketing']);
        Specialite::create(['nom' => 'Avocat']);
        Specialite::create(['nom' => 'Ingénieur']);

        $this->call([
            // StageSeeder::class,
            // ContactSeeder::class,
            // OpportunitySeeder::class,
        ]);
    }
}

