<?php

namespace App\Filament\Resources\SpecialiteResource\Pages;

use App\Filament\Resources\SpecialiteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpecialite extends EditRecord
{
    protected static string $resource = SpecialiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
