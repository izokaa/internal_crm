<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditExpense extends EditRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $expenseData = collect($data)->except('piecesJointes')->toArray();
        $montantHt = $expenseData['montant_ht'];
        $tva = $expenseData['tva'];
        $expenseData['montant_ttc'] = $montantHt * (1 + ($tva / 100));

        $record->update($expenseData);

        if (isset($data['piecesJointes'])) {
            $record->piecesJointes()->delete();
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $record->piecesJointes()->create($pieceJointeData);
            }
        }

        return $record;
    }

}
