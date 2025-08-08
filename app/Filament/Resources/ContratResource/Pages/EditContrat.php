<?php

namespace App\Filament\Resources\ContratResource\Pages;

use App\Filament\Resources\ContratResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditContrat extends EditRecord
{
    protected static string $resource = ContratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $contratData = collect($data)->except('piecesJointes')->toArray();
        $montantHt = $contratData['montant_ht'];
        $tva = $contratData['tva'];
        $contratData['montant_ttc'] = $montantHt * (1 + ($tva / 100));

        $record->update($contratData);

        if (isset($data['piecesJointes'])) {
            $record->piecesJointes()->delete();
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $record->piecesJointes()->create($pieceJointeData);
            }
        }

        return $record;
    }
}