<?php

namespace App\Filament\Resources\EtapePipelineResource\Pages;

use App\Filament\Resources\EtapePipelineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEtapePipeline extends EditRecord
{
    protected static string $resource = EtapePipelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
