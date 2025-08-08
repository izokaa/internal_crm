<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Infolist;

class ViewOpportunity extends ViewRecord
{
    protected static string $resource = OpportunityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function getRecord(): Model
    {
        return parent::getRecord()->load(['pipeline.etapePipelines', 'etapePipeline']);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return OpportunityResource::infolist($infolist);
    }
}