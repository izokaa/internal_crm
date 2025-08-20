<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Opportunity;
use App\Filament\Pages\OpportunityBoardPage;

class ListOpportunities extends ListRecords
{
    protected static string $resource = OpportunityResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pipeline')
                ->label('Pipeline')
                ->url(OpportunityBoardPage::getUrl())
                ->icon('heroicon-o-view-columns'),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Tout' => Tab::make()
                ->badge(Opportunity::count())
                ->badgeColor('gray'),
            'Ouverte' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Ouverte'))
                ->badge(Opportunity::where('status', 'Ouverte')->count())
                ->badgeColor('info'),
            'Gagnée' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Gagnée'))
                ->badge(Opportunity::where('status', 'Gagnée')->count())
                ->badgeColor('success'),
            'Perdue' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Perdue'))
                ->badge(Opportunity::where('status', 'Perdue')->count())
                ->badgeColor('danger'),
            'En retard' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'En retard'))
                ->badge(Opportunity::where('status', 'En retard')->count())
                ->badgeColor('warning'),
            'Annulée' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Annulée'))
                ->badge(Opportunity::where('status', 'Annulée')->count())
                ->badgeColor('gray'),
            'Fermée' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Fermée'))
                ->badge(Opportunity::where('status', 'Fermée')->count())
                ->badgeColor('primary'),
        ];
    }
}
