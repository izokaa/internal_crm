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
            'All' => Tab::make()
                ->badge(Opportunity::count())
                ->badgeColor('gray'),
            'Clients' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereHas('contact', fn ($query) => $query->where('type', 'client'));
                })
                ->badge(Opportunity::whereHas('contact', fn ($query) => $query->where('type', 'client'))->count())
                ->badgeColor('success'),
            'Prospects' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereHas('contact', fn ($query) => $query->where('type', 'prospect'));
                })
                ->badge(Opportunity::whereHas('contact', fn ($query) => $query->where('type', 'prospect'))->count())
                ->badgeColor('info')
        ];
    }
}