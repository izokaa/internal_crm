<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Enums\OpportunityStatut;
use App\Filament\Resources\OpportunityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Opportunity;
use App\Filament\Pages\OpportunityBoardPage;
use App\Models\Contact;
use App\Models\EtapePipeline;
use App\Models\Pipeline;
use App\Models\Source;
use Illuminate\Support\Collection;

class ListOpportunities extends ListRecords
{
    protected static string $resource = OpportunityResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pipeline')
                ->label('Pipeline')
                ->url(OpportunityBoardPage::getUrl())
                ->icon('heroicon-o-view-columns'),
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->label('Importer')
                ->slideOver()
                ->processCollectionUsing(function (string $modelClass, Collection $rows) {
                    foreach ($rows as $row) {
                        // Normaliser les clés (minuscules + trim sans accents)
                        $data = collect($row)->mapWithKeys(function ($value, $key) {
                            $normalizedKey = strtolower(trim($key));
                            $normalizedKey = str_replace([' ', '-', 'é'], ['_', '_', 'e'], $normalizedKey);
                            return [$normalizedKey => $value];
                        });


                        // Récupération des champs Excel
                        $titre       = $data->get('titre');
                        $montantEstime       = $data->get('montant_estime');
                        $montantReel       = $data->get('montant_reel');
                        $devise          = $data->get('devise');
                        $contactEmail = $data->get('email_contact');
                        $probabilite             = $data->get('probabilite');
                        $dateEcheance             = $data->get('date_echeance');
                        $pipelineName          = $data->get('pipeline');
                        $etapePipelineName          = $data->get('etape_pipeline');
                        $sourceName          = $data->get('source');
                        $statut          = $data->get('statut');

                        // Relations

                        $contact = $contactEmail ? Contact::firstOrCreate(['email' => $contactEmail]) : null;
                        $pipeline = $pipelineName ? Pipeline::firstOrCreate(['nom' => $pipelineName]) : null;
                        $etapePipeline = $etapePipelineName ? EtapePipeline::firstOrCreate(['nom' => $etapePipelineName]) : null;
                        $source = $sourceName ? Source::firstOrCreate(['nom' => $sourceName]) : null;


                        // Création du contrat
                        Opportunity::create([
                            'titre'     => $titre,
                            'montant_estime'       => $montantEstime,
                            'montant_reel'      => $montantReel,
                            'date_echeance' => $dateEcheance,
                            'probabilite' => $probabilite,
                            'devise'           => $devise,
                            'prefix' => 'OPPO',
                            'contact_id' => $contact->id,
                            'pipeline_id' => $pipeline->id,
                            'source_id' => $source->id,
                            'etape_pipeline_id' => $etapePipeline->id,
                            'status'  =>  collect(OpportunityStatut::cases())->first(fn ($case) => $case->getLabel() === $statut)?->value,
                        ]);
                    }

                    return $rows;
                })
        ];
    }

    public function getTabs(): array
    {
        return [
            'Tout' => Tab::make()
                ->badge(Opportunity::count())
                ->badgeColor('gray'),
            'Ouverte' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Ouverte'))
                ->badge(Opportunity::where('status', 'Ouverte')->count())
                ->badgeColor('info'),
            'Gagnée' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Gagnée'))
                ->badge(Opportunity::where('status', 'Gagnée')->count())
                ->badgeColor('success'),
            'Perdue' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Perdue'))
                ->badge(Opportunity::where('status', 'Perdue')->count())
                ->badgeColor('danger'),
            'En retard' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'En retard'))
                ->badge(Opportunity::where('status', 'En retard')->count())
                ->badgeColor('warning'),
            'Annulée' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Annulée'))
                ->badge(Opportunity::where('status', 'Annulée')->count())
                ->badgeColor('gray'),
            'Fermée' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Fermée'))
                ->badge(Opportunity::where('status', 'Fermée')->count())
                ->badgeColor('primary'),
        ];
    }
}
