<?php

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;

class CreateDevis extends CreateRecord
{
    protected static string $resource = DevisResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $devisData = collect($data)->except('piecesJointes')->toArray();

        if (isset($data['piecesJointes'])) {
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $devis->piecesJointes()->create($pieceJointeData);
            }
        }

        $totalHt = $devisData['total_ht'];
        $tva = $devisData['tva'];
        $devisData['total_ttc'] = $totalHt * (1 + ($tva / 100));

        $devis = static::getModel()::create($devisData);


        if (isset($data['piecesJointes'])) {
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $devis->piecesJointes()->create($pieceJointeData);
            }
        }


        return $devis;
    }

}
