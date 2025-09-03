<?php

namespace App\Filament\Resources\DevisResource\Pages;

use App\Enums\DevisStatus;
use App\Filament\Resources\DevisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\Contact;
use App\Models\Devis;
use Carbon\Carbon;

class ListDevis extends ListRecords
{
    protected static string $resource = DevisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()
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
                        $numeroDevis   = $data->get('numero_de_devis');
                        $totalHt       = $data->get('total_ht');
                        $totalTtc      = $data->get('total_ttc');
                        $tva = $data->get('tva');
                        $devise          = $data->get('devise');
                        $dateDevis     = $data->get('date_devis');
                        $dateEmission = $data->get('date_emission');
                        $contactEmail      = $data->get('email_du_contact');
                        $remise      = $data->get('remise');
                        $statut          = $data->get('status');

                        // Relations : Client, Business Unit, Service
                        $contact = $contactEmail
                            ? Contact::firstOrCreate(['email' => $contactEmail])
                            : null;

                        // Création du contrat
                        Devis::create([
                            'quote_number'   => $numeroDevis,
                            'date_devis'     => $dateDevis ? Carbon::parse($dateDevis) : null,
                            'date_emission'       => $dateEmission ? Carbon::parse($dateEmission) : null,
                            'montant_ht'       => $totalHt,
                            'montant_ttc'      => $totalTtc,
                            'tva'      => $tva,
                            'devise'           => $devise,
                            'tva'              => $tva,
                            'contact_id'        => $contact?->id,
                            'remise'        => $remise,
                            'status'  =>  collect(DevisStatus::cases())->first(fn ($case) => $case->getLabel() === $statut)?->value,
                        ]);
                    }

                    return $rows;
                })
        ];
    }
}
