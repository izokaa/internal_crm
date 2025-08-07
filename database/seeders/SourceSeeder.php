<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Source;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Source::create(['nom' => 'Site Web']);
        Source::create(['nom' => 'Réseaux Sociaux']);
        Source::create(['nom' => 'Recommandation']);
        Source::create(['nom' => 'Publicité en ligne']);
        Source::create(['nom' => 'Événement/Salon']);
        Source::create(['nom' => 'Partenariat']);
        Source::create(['nom' => 'Cold Calling']);
        Source::create(['nom' => 'Email Marketing']);
    }
}