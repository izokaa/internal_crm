<?php

namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stage::create(['name' => 'Nouveau', 'order' => 1]);
        Stage::create(['name' => 'Qualification', 'order' => 2]);
        Stage::create(['name' => 'Proposition', 'order' => 3]);
        Stage::create(['name' => 'Négociation', 'order' => 4]);
        Stage::create(['name' => 'Gagné', 'order' => 5]);
        Stage::create(['name' => 'Perdu', 'order' => 6]);
    }
}
