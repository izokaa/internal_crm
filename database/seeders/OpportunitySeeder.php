<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Stage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = Stage::all();
        $contacts = Contact::all();

        if ($contacts->isEmpty() || $stages->isEmpty()) {
            $this->command->info('Please seed Contacts and Stages first!');
            return;
        }

        Opportunity::factory(20)->create([
            'contact_id' => fn() => $contacts->random()->id,
            'stage_id' => fn() => $stages->random()->id,
        ]);
    }
}
