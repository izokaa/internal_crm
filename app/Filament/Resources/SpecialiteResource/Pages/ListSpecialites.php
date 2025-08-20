<?php

namespace App\Filament\Resources\SpecialiteResource\Pages;

use App\Filament\Resources\SpecialiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpecialites extends ListRecords
{
    protected static string $resource = SpecialiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
