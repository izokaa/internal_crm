<?php

namespace Database\Seeders;

use App\Models\Label;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $callLabels = [
            '🔍 Appel prospection',
            '☎  Appel commercial',
            '🤝 Appel suivi client'];

        $taskLabels = [
            '📝 Réaliser chiffrage',
            '📅 Réunion / RDV client',
            '📋 Compte-rendu RDV',
            '🔄 Relance devis',
            '📮 Envoi facture',
            '⏰ Relance facture',
            '📦 Livraison',
        ];

        $eventLabels = [
            '📦 Livraison',
            '🚗 Déplacement',
        ];

        foreach ($taskLabels as $label) {
            Label::create([
                'value' => $label,
                'for_task' => true,
                'for_event' => false,
                'for_call' => false,
                'couleur' => '#F0FF0F',
            ]);
        }

        foreach ($eventLabels as $label) {
            Label::create([
                'value' => $label,
                'for_task' => false,
                'for_event' => true,
                'for_call' => false,
                'couleur' => '#F0FF0F',
            ]);
        }

        foreach ($callLabels as $label) {
            Label::create([
                'value' => $label,
                'for_task' => false,
                'for_event' => false,
                'for_call' => true,
                'couleur' => '#F0FF0F',
            ]);
        }


    }
}
