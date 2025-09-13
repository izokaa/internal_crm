<?php

namespace App\Filament\Resources\ContratResource\Pages;

use App\Enums\ContratStatus;
use App\Filament\Resources\ContratResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateContrat extends CreateRecord
{
    protected static string $resource = ContratResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $contratData = collect($data)->except('piecesJointes')->toArray();
        $montantHt = $contratData['montant_ht'];
        $tva = $contratData['tva'];
        $contratData['montant_ttc'] = $montantHt * (1 + ($tva / 100));

        $contrat = static::getModel()::create($contratData);

        if (isset($data['piecesJointes'])) {
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $contrat->piecesJointes()->create($pieceJointeData);
            }
        }


        return $contrat;
    }


}
