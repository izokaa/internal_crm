<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pays;
use App\Models\Specialite;
use App\Models\Ville;
use App\Models\BusinessUnit;
use App\Models\Service;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $maroc = Pays::create(['nom' => 'Maroc']);
        $france = Pays::create(['nom' => 'France']);
        $allemagne = Pays::create(['nom' => 'Allemagne']);
        $espagne = Pays::create(['nom' => 'Espagne']);

        Ville::create(['nom' => 'Casablanca', 'pays_id' => $maroc->id]);
        Ville::create(['nom' => 'Rabat', 'pays_id' => $maroc->id]);
        Ville::create(['nom' => 'Paris', 'pays_id' => $france->id]);
        Ville::create(['nom' => 'Lyon', 'pays_id' => $france->id]);
        Ville::create(['nom' => 'Berlin', 'pays_id' => $allemagne->id]);
        Ville::create(['nom' => 'Madrid', 'pays_id' => $espagne->id]);

        Specialite::create(['nom' => 'Médecin Généraliste']);
        Specialite::create(['nom' => 'Développeur Web']);
        Specialite::create(['nom' => 'Consultant Marketing']);
        Specialite::create(['nom' => 'Avocat']);
        Specialite::create(['nom' => 'Ingénieur']);

        $bu1 = BusinessUnit::create(['nom' => 'Ventes']);
        $bu2 = BusinessUnit::create(['nom' => 'Support']);

        Service::create(['nom' => 'Service Client', 'business_unit_id' => $bu2->id]);
        Service::create(['nom' => 'Support Technique', 'business_unit_id' => $bu2->id]);
        Service::create(['nom' => 'Prospection', 'business_unit_id' => $bu1->id]);
        Service::create(['nom' => 'Fidélisation', 'business_unit_id' => $bu1->id]);

        $this->call([
            // StageSeeder::class,
            // ContactSeeder::class,
            // OpportunitySeeder::class,
        ]);
    }
}
