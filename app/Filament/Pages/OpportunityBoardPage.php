<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;

class OpportunityBoardPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Vue pipeline';
    protected static ?string $navigationGroup = 'Opportunités';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.opportunity-board-page';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createOpportunity')
                ->label('Créer une nouvelle opportunité')
                ->url(route('filament.admin.resources.opportunities.create'))
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getHeading(): string
    {
        return 'Opportunités dans le pipeline';
    }
}
