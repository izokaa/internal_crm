<?php

namespace App\Filament\Resources\FactureResource\Pages;

use App\Filament\Resources\FactureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateFacture extends CreateRecord
{
    protected static string $resource = FactureResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $factureData = collect($data)->except('piecesJointes')->toArray();

        if (isset($data['piecesJointes'])) {
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $facture->piecesJointes()->create($pieceJointeData);
            }
        }

        $montantHt = $factureData['montant_ht'];
        $tva = $factureData['tva'];
        $factureData['montant_ttc'] = $montantHt * (1 + ($tva / 100));

        $facture = static::getModel()::create($factureData);


        if (isset($data['piecesJointes'])) {
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $facture->piecesJointes()->create($pieceJointeData);
            }
        }


        return $facture;
    }


}