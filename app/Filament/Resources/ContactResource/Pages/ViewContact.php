<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('nom'),
                TextEntry::make('prenom'),
                TextEntry::make('email'),
                TextEntry::make('telephone'),
                TextEntry::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'prospect' => 'info',
                        'client' => 'success',
                        default => 'gray',
                    }),
                TextEntry::make('ville.nom')->label('Ville'),
                TextEntry::make('ville.pays.nom')->label('Pays'),
                TextEntry::make('specialite.nom')->label('Spécialité'),
                TextEntry::make('businessUnit.nom')->label('Unité Commerciale'),
                TextEntry::make('service.nom')->label('Service'),
                TextEntry::make('created_at')->label('Date de création')->dateTime(),
                TextEntry::make('updated_at')->label('Date de modification')->dateTime(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}