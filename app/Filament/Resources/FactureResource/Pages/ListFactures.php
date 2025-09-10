<?php

namespace App\Filament\Resources\FactureResource\Pages;

use App\Enums\FactureStatus;
use App\Filament\Resources\FactureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Contrat;
use App\Models\Facture;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ListFactures extends ListRecords
{
    protected static string $resource = FactureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->visible(auth()->user()->can('import_facture'))
                ->slideOver()
                ->label('Importer')
                ->processCollectionUsing(function (string $modelClass, Collection $rows) {
                    foreach ($rows as $row) {
                        // Normaliser les clés (minuscules + trim sans accents)
                        $data = collect($row)->mapWithKeys(function ($value, $key) {
                            $normalizedKey = strtolower(trim($key));
                            $normalizedKey = str_replace([' ', '-', 'é'], ['_', '_', 'e'], $normalizedKey);
                            return [$normalizedKey => $value];
                        });


                        // Récupération des champs Excel
                        $numeroFacture   = $data->get('numero_facture');
                        $dateFacture     = $data->get('date_facture');
                        $echeancePayment  = $data->get('echeance_paiement');
                        $contratId  = $data->get('numero_de_contrat');
                        $montantHt       = $data->get('montant_ht');
                        $montantTtc      = $data->get('montant_ttc');
                        $devise          = $data->get('devise');
                        $tva             = $data->get('tva');
                        $statut          = $data->get('status');
                        // Relations

                        Log::info($data);
                        $contrat = $contratId ? Contrat::firstOrCreate(['numero_contrat' => $contratId]) : null;

                        // Création du contrat
                        Facture::create([
                            'numero_facture'   => $numeroFacture,
                            'date_facture'     => $dateFacture ? Carbon::parse($dateFacture) : null,
                            'echeance_payment'       => $echeancePayment ? Carbon::parse($echeancePayment) : null,
                            'contrat_id' => $contrat->id,
                            'montant_ht'       => $montantHt,
                            'montant_ttc'      => $montantTtc,
                            'devise'           => $devise,
                            'tva'              => $tva,
                            'status'  =>  collect(FactureStatus::cases())->first(fn ($case) => $case->getLabel() === $statut)?->value,
                        ]);
                    }

                    return $rows;
                })
        ];
    }
}
