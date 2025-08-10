<?php

namespace App\Filament\Resources\ContratResource\Pages;

use App\Filament\Resources\ContratResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Contrat;

class ListContrats extends ListRecords
{
    protected static string $resource = ContratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Tout' => Tab::make()
                ->badge(Contrat::count())
                ->badgeColor('gray'),
            'Actif' => Tab::make()
                        ->modifyQueryUsing(fn (Builder $query) => $query->where('date_fin', '>', now()))
                        ->badge(Contrat::where('date_fin', '>', now())->count())
                        ->badgeColor('success'),
            'Expiré' => Tab::make()
                        ->modifyQueryUsing(fn (Builder $query) => $query->where('date_fin', '<=', now()))
                        ->badge(Contrat::where('date_fin', '<=', now())->count())
                        ->badgeColor('danger'),
        ];
    }
}
