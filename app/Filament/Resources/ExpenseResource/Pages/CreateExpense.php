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
        $expenseData = collect($data)->toArray();
        $montantHt = $expenseData['montant_ht'];
        $tva = $expenseData['tva'];
        $expenseData['montant_ttc'] = $montantHt * (1 + ($tva / 100));

        $expense = static::getModel()::create($expenseData);

    
        return $expense;
    }

}
