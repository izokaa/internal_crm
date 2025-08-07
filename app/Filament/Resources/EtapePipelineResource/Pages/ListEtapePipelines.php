<?php

namespace App\Filament\Resources\EtapePipelineResource\Pages;

use App\Filament\Resources\EtapePipelineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEtapePipelines extends ListRecords
{
    protected static string $resource = EtapePipelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
