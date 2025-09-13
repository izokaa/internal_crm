<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;
    protected static ?string $title = 'Liste des Fournisseurs';

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $query->where('type', 'fournisseur');

        return $query;

    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
