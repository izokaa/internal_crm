<?php

namespace App\Filament\Resources\ContratResource\Pages;

use App\Filament\Resources\ContratResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContrat extends ViewRecord
{
    protected static string $resource = ContratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
