<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pays;
use App\Models\Specialite;
use App\Models\Ville;
use App\Models\BusinessUnit;
use App\Models\Service;
use App\Models\Pipeline;
use App\Models\EtapePipeline;
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

        $pipelineVente = Pipeline::create(['nom' => 'Pipeline de Vente Standard']);
        EtapePipeline::create(['nom' => 'Qualification', 'ordre' => 1, 'pipeline_id' => $pipelineVente->id]);
        EtapePipeline::create(['nom' => 'Proposition', 'ordre' => 2, 'pipeline_id' => $pipelineVente->id]);
        EtapePipeline::create(['nom' => 'Négociation', 'ordre' => 3, 'pipeline_id' => $pipelineVente->id]);
        EtapePipeline::create(['nom' => 'Fermeture', 'ordre' => 4, 'pipeline_id' => $pipelineVente->id]);

        $pipelineSupport = Pipeline::create(['nom' => 'Pipeline de Support Client']);
        EtapePipeline::create(['nom' => 'Ouverture Ticket', 'ordre' => 1, 'pipeline_id' => $pipelineSupport->id]);
        EtapePipeline::create(['nom' => 'Diagnostic', 'ordre' => 2, 'pipeline_id' => $pipelineSupport->id]);
        EtapePipeline::create(['nom' => 'Résolution', 'ordre' => 3, 'pipeline_id' => $pipelineSupport->id]);
        EtapePipeline::create(['nom' => 'Clôture', 'ordre' => 4, 'pipeline_id' => $pipelineSupport->id]);

        $this->call([
            // StageSeeder::class,
            // ContactSeeder::class,
            // OpportunitySeeder::class,
        ]);
    }
}