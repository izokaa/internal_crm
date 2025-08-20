<?php

namespace App\Filament\Resources\PipelineResource\Pages;

use App\Filament\Resources\PipelineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPipeline extends EditRecord
{
    protected static string $resource = PipelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
