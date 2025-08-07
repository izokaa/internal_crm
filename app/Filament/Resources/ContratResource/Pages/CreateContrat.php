<?php

namespace App\Filament\Resources\ContratResource\Pages;

use App\Filament\Resources\ContratResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateContrat extends CreateRecord
{
    protected static string $resource = ContratResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $montantHt = $data['montant_ht'];
        $tva = $data['tva'];
        $data['montant_ttc'] = $montantHt * (1 + ($tva / 100));

        return $data;
    }
}
