<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use App\Models\Ville;
use App\Models\Pays;
use App\Models\Specialite;
use App\Models\BusinessUnit;
use App\Models\Service;
use App\Models\Contact;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->slideOver()
                ->label('Importer')
                ->validateUsing([
                    'telephone' => 'unique:contacts,telephone',
                    'email' => 'unique:contacts,email',
                ])
                ->processCollectionUsing(function (string $modelClass, Collection $rows) {
                    foreach ($rows as $row) {
                        // Normaliser les clés (minuscules + trim sans accents)
                        $data = collect($row)->mapWithKeys(function ($value, $key) {
                            $normalizedKey = strtolower(trim($key));
                            return [$normalizedKey => $value];
                        });

                        // Récupération des champs Excel
                        $title        = $data->get('titre');
                        $nom          = $data->get('nom');
                        $prenom       = $data->get('prenom');
                        $email        = $data->get('email');
                        $telephone    = $data->get('telephone');
                        $type         = $data->get('type');
                        $companyType  = $data->get('type_societe') ?? 'individual';
                        $companyName  = $data->get('nom_societe');
                        $website      = $data->get('site web');
                        $adresse      = $data->get('adresse');
                        $villeName    = $data->get('ville');
                        $paysName     = $data->get('pays');
                        $specName     = $data->get('spécialité') ?? $data->get('specialite');
                        $unitName     = $data->get('Unité_Commerciale') ?? $data->get('unite_commerciale');
                        $serviceName  = $data->get('service');

                        // Création ou récupération du pays (si modèle existe)
                        $pays = $paysName ? Pays::firstOrCreate(['nom' => $paysName]) : null;

                        // Création ou récupération de la ville
                        $ville = $villeName
                            ? Ville::firstOrCreate([
                                'nom'    => $villeName,
                                'pays_id' => $pays?->id,
                            ])
                            : null;

                        // Création ou récupération des relations
                        $specialite = $specName ? Specialite::firstOrCreate(['nom' => $specName]) : null;
                        $unit       = $unitName ? BusinessUnit::firstOrCreate(['nom' => $unitName]) : null;
                        $service    = $serviceName ? Service::firstOrCreate(['nom' => $serviceName]) : null;

                        // Création du contact
                        Contact::create([
                            'title'              => $title,
                            'nom'              => $nom,
                            'prenom'           => $prenom,
                            'email'            => $email,
                            'telephone'        => $telephone,
                            'type'             => $type,
                            'company_type'     => $companyType,
                            'company_name'     => $companyName,
                            'website'          => $website,
                            'adresse'          => $adresse,
                            'ville_id'         => $ville?->id,
                            'specialite_id'    => $specialite?->id,
                            'business_unit_id' => $unit?->id,
                            'service_id'       => $service?->id,
                            'profile_picture'  => null,
                            'custom_fields'    => [],
                        ]);
                    }

                    return $rows;
                }),
            Actions\CreateAction::make(),
        ];
    }
}
