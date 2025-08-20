<?php

namespace App\Filament\Resources\PaysResource\Pages;

use App\Filament\Resources\PaysResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPays extends EditRecord
{
    protected static string $resource = PaysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
