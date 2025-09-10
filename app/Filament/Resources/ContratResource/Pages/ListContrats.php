<?php

namespace App\Filament\Resources\ContratResource\Pages;

use App\Filament\Resources\ContratResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Contrat;
use App\Enums\ContratStatus;
use App\Enums\ModePayment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\Service;
use App\Models\BusinessUnit;
use App\Models\Contact;
use Carbon\Carbon;

class ListContrats extends ListRecords
{
    protected static string $resource = ContratResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->visible(auth()->user()->can('import_contrat'))
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
                        $numeroContrat   = $data->get('numero_de_contrat');
                        $dateContrat     = $data->get('date_contrat');
                        $dateDebut       = $data->get('date_debut_abonnement');
                        $dateFin         = $data->get('date_fin_abonnement');
                        $periodeContrat  = $data->get('periode_contrat');
                        $montantHt       = $data->get('montant_hors_taxe_ht');
                        $montantTtc      = $data->get('montant_ttc');
                        $devise          = $data->get('devise');
                        $tva             = $data->get('tva');
                        $clientEmail      = $data->get('email_du_client');
                        $clientNom      = $data->get('nom_du_client');
                        $clientPrenom      = $data->get('prenom_du_client');
                        $unitName        = $data->get('business_unit');
                        $serviceName     = $data->get('service');
                        $statut          = $data->get('statut_du_contrat');
                        $modePaiement    = $data->get('mode_de_paiement');

                        // Relations : Client, Business Unit, Service
                        $client = $clientNom
                            ? Contact::firstOrCreate(['email' => $clientEmail])
                            : null;

                        $unit = $unitName
                            ? BusinessUnit::firstOrCreate(['nom' => $unitName])
                            : null;

                        $service = $serviceName
                            ? Service::firstOrCreate(['nom' => $serviceName])
                            : null;

                        // Création du contrat
                        Contrat::create([
                            'numero_contrat'   => $numeroContrat,
                            'date_contrat'     => $dateContrat ? Carbon::parse($dateContrat) : null,
                            'date_debut'       => $dateDebut ? Carbon::parse($dateDebut) : null,
                            'date_fin'         => $dateFin ? Carbon::parse($dateFin) : null,
                            'periode_contrat'  => $periodeContrat,
                            'periode_unite'    => null, // à compléter si tu as l’info
                            'montant_ht'       => $montantHt,
                            'montant_ttc'      => $montantTtc,
                            'devise'           => $devise,
                            'tva'              => $tva,
                            'client_id'        => $client?->id,
                            'business_unit_id' => $unit?->id ?? null,
                            'service_id'       => $service?->id ?? null,
                            'status'  =>  collect(ContratStatus::cases())->first(fn ($case) => $case->getLabel() === $statut)?->value,
                            'mode_payment'  =>  collect(ModePayment::cases())->first(fn ($case) => $case->getLabel() === $modePaiement)?->value,
                            'renewable_count'  => 0,
                        ]);
                    }

                    return $rows;
                }),
            Actions\CreateAction::make(),
        ];
    }



    public function getTabs(): array
    {
        return [
            'Tout' => Tab::make()
                ->badge(Contrat::count())
                ->badgeColor('gray'),
            'Actifs' => Tab::make()
                ->badge(Contrat::where('status', ContratStatus::ACTIVE->value)->count())
                ->badgeColor('success')
                ->query(fn (Builder $query) => $query->where('status', ContratStatus::ACTIVE->value)),
            'Exirés' => Tab::make()
                ->badge(Contrat::where('status', ContratStatus::EXPIRED->value)->count())
                ->badgeColor('danger')
                ->query(fn (Builder $query) => $query->where('status', ContratStatus::EXPIRED->value)),
        ];
    }
}
