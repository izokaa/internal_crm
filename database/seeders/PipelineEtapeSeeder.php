<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pipeline;
use App\Models\EtapePipeline;

class PipelineEtapeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pipelineVente = Pipeline::create(['nom' => 'Pipeline de Vente Standard']);
        EtapePipeline::create(['nom' => 'Qualification', 'icon' => 'heroicon-o-magnifying-glass', 'ordre' => 1, 'pipeline_id' => $pipelineVente->id]);
        EtapePipeline::create(['nom' => 'Proposition', 'icon' => 'heroicon-o-document-text', 'ordre' => 2, 'pipeline_id' => $pipelineVente->id]);
        EtapePipeline::create(['nom' => 'Négociation', 'icon' => 'heroicon-o-currency-dollar', 'ordre' => 3, 'pipeline_id' => $pipelineVente->id]);
        EtapePipeline::create(['nom' => 'Fermeture', 'icon' => 'heroicon-o-check-circle', 'ordre' => 4, 'pipeline_id' => $pipelineVente->id]);

        $pipelineSupport = Pipeline::create(['nom' => 'Pipeline de Support Client']);
        EtapePipeline::create(['nom' => 'Ouverture Ticket', 'icon' => 'heroicon-o-ticket', 'ordre' => 1, 'pipeline_id' => $pipelineSupport->id]);
        EtapePipeline::create(['nom' => 'Diagnostic', 'icon' => 'heroicon-o-bug-ant', 'ordre' => 2, 'pipeline_id' => $pipelineSupport->id]);
        EtapePipeline::create(['nom' => 'Résolution', 'icon' => 'heroicon-o-wrench-screwdriver', 'ordre' => 3, 'pipeline_id' => $pipelineSupport->id]);
        EtapePipeline::create(['nom' => 'Clôture', 'icon' => 'heroicon-o-archive-box', 'ordre' => 4, 'pipeline_id' => $pipelineSupport->id]);

        $pipelineRecrutement = Pipeline::create(['nom' => 'Pipeline de Recrutement']);
        EtapePipeline::create(['nom' => 'Analyse CV', 'icon' => 'heroicon-o-clipboard-document-list', 'ordre' => 1, 'pipeline_id' => $pipelineRecrutement->id]);
        EtapePipeline::create(['nom' => 'Entretien RH', 'icon' => 'heroicon-o-users', 'ordre' => 2, 'pipeline_id' => $pipelineRecrutement->id]);
        EtapePipeline::create(['nom' => 'Entretien Technique', 'icon' => 'heroicon-o-code-bracket', 'ordre' => 3, 'pipeline_id' => $pipelineRecrutement->id]);
        EtapePipeline::create(['nom' => 'Offre', 'icon' => 'heroicon-o-gift', 'ordre' => 4, 'pipeline_id' => $pipelineRecrutement->id]);
    }
}
