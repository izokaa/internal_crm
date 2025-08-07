<?php

namespace App\Filament\Resources\PipelineResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Forms\Form;

class ViewPipeline extends ViewRecord
{
    protected static string $resource = \App\Filament\Resources\PipelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Les champs du formulaire sont gérés par le RelationManager
            ]);
    }

    public function getTitle(): string
    {
        return $this->getRecord()->nom;
    }
}