<?php

namespace App\Filament\Resources\ContratResource\Pages;

use App\Filament\Resources\ContratResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Contrat;
use App\Enums\ContratStatus;

class ListContrats extends ListRecords
{
    protected static string $resource = ContratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->slideOver()
                ->color("primary"),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Tout' => Tab::make()
                ->badge(Contrat::count())
                ->badgeColor('gray'),
            'Actifs' => Tab::make()
                ->badge(Contrat::where('status', ContratStatus::ACTIVE->value)->count())
                ->badgeColor('success')
                ->query(fn (Builder $query) => $query->where('status', ContratStatus::ACTIVE->value)),
            'Exirés' => Tab::make()
                ->badge(Contrat::where('status', ContratStatus::EXPIRED->value)->count())
                ->badgeColor('danger')
                ->query(fn (Builder $query) => $query->where('status', ContratStatus::EXPIRED->value)),
            ];
    }
}
