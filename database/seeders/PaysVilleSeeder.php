<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pays;
use App\Models\Ville;

class PaysVilleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maroc = Pays::create(['nom' => 'Maroc']);
        $france = Pays::create(['nom' => 'France']);
        $allemagne = Pays::create(['nom' => 'Allemagne']);
        $espagne = Pays::create(['nom' => 'Espagne']);
        $usa = Pays::create(['nom' => 'États-Unis']);
        $canada = Pays::create(['nom' => 'Canada']);

        Ville::create(['nom' => 'Casablanca', 'pays_id' => $maroc->id]);
        Ville::create(['nom' => 'Rabat', 'pays_id' => $maroc->id]);
        Ville::create(['nom' => 'Marrakech', 'pays_id' => $maroc->id]);
        Ville::create(['nom' => 'Tanger', 'pays_id' => $maroc->id]);

        Ville::create(['nom' => 'Paris', 'pays_id' => $france->id]);
        Ville::create(['nom' => 'Lyon', 'pays_id' => $france->id]);
        Ville::create(['nom' => 'Marseille', 'pays_id' => $france->id]);

        Ville::create(['nom' => 'Berlin', 'pays_id' => $allemagne->id]);
        Ville::create(['nom' => 'Munich', 'pays_id' => $allemagne->id]);

        Ville::create(['nom' => 'Madrid', 'pays_id' => $espagne->id]);
        Ville::create(['nom' => 'Barcelone', 'pays_id' => $espagne->id]);

        Ville::create(['nom' => 'New York', 'pays_id' => $usa->id]);
        Ville::create(['nom' => 'Los Angeles', 'pays_id' => $usa->id]);

        Ville::create(['nom' => 'Montréal', 'pays_id' => $canada->id]);
        Ville::create(['nom' => 'Toronto', 'pays_id' => $canada->id]);
    }
}