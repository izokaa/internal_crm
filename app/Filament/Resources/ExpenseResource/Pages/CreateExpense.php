<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $expenseData = collect($data)->except('piecesJointes')->toArray();

        $expense = static::getModel()::create($expenseData);

        if (isset($data['piecesJointes'])) {
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $expense->piecesJointes()->create($pieceJointeData);
            }
        }

        return $expense;
    }

}
