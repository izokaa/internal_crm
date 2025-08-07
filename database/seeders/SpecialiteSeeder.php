<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialite;

class SpecialiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Specialite::create(['nom' => 'Médecin Généraliste']);
        Specialite::create(['nom' => 'Développeur Web']);
        Specialite::create(['nom' => 'Consultant Marketing']);
        Specialite::create(['nom' => 'Avocat']);
        Specialite::create(['nom' => 'Ingénieur']);
        Specialite::create(['nom' => 'Designer Graphique']);
        Specialite::create(['nom' => 'Comptable']);
        Specialite::create(['nom' => 'Architecte']);
        Specialite::create(['nom' => 'Journaliste']);
        Specialite::create(['nom' => 'Enseignant']);
    }
}