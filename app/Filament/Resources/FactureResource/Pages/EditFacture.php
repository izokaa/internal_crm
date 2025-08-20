<?php

namespace App\Filament\Resources\FactureResource\Pages;

use App\Filament\Resources\FactureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;


class EditFacture extends EditRecord
{
    protected static string $resource = FactureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $factureData = collect($data)->except('piecesJointes')->toArray();

        $montantHt = $factureData['montant_ht'];
        $tva = $factureData['tva'];
        $factureData['montant_ttc'] = $montantHt * (1 + ($tva / 100));

        $record->update($factureData);

        if (isset($data['piecesJointes'])) {
            $record->piecesJointes()->delete();
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $record->piecesJointes()->create($pieceJointeData);
            }
        }

        return $record;
    }


}
