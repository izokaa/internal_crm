<?php

namespace App\Filament\Pages;

use App\Traits\HasActiveIcon;
use Filament\Pages\Page;
use Filament\Actions\Action;

class OpportunityBoardPage extends Page
{
    use HasActiveIcon;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationActiveIcon = 'heroicon-s-view-columns';
    protected static ?string $navigationLabel = 'Vue pipeline';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.opportunity-board-page';

    protected static ?string $navigationGroup = 'CRM';

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
