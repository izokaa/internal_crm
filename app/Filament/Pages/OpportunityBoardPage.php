<?php

namespace App\Filament\Pages;

use App\Traits\HasActiveIcon;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Actions\Action;

class OpportunityBoardPage extends Page
{
    use HasActiveIcon;
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationActiveIcon = 'heroicon-s-view-columns';
    protected static ?string $navigationLabel = 'Vue pipeline';
    protected static ?string $title = 'Vue pipeline';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.opportunity-board-page';

    protected static ?string $navigationGroup = 'CRM';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createOpportunity')
                ->visible(auth()->user()->can('create_opportunity'))
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
