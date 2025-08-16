<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations Personnelles')
                    ->schema([
                        ImageEntry::make('profile_picture')
                            ->label('Photo de profil')
                            ->visibility('public')
                            ->disk('public')
                            ->width(100)
                            ->square()
                            ->defaultImageUrl('https://ui-avatars.com/api/?name=Contact&background=random&color=fff'),
                            TextEntry::make('type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'prospect' => 'info',
                                'client' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('nom')
                            ->label('Nom et Prénom')
                            ->getStateUsing(fn ($record) => "{$record->title} {$record->nom} {$record->prenom}"),
                        
                        TextEntry::make('email'),
                        TextEntry::make('telephone'),   
                       TextEntry::make('company_type')
                            ->label('Type de société'),
                        TextEntry::make('company_name')
                            ->label('Nom de la société'),
                        TextEntry::make('website')
                            ->label('Site Web'),
                        
                    ])->columns(2),

                Section::make('Localisation')
                    ->schema([
                        TextEntry::make('ville.nom')->label('Ville'),
                        TextEntry::make('ville.pays.nom')->label('Pays'),
                        TextEntry::make('adresse')->label('Adresse'),   
                    ])->columns(2),

                Section::make('Professionnel')
                    ->schema([
                        TextEntry::make('specialite.nom')->label('Spécialité'),
                        TextEntry::make('businessUnit.nom')->label('Unité Commerciale'),
                        TextEntry::make('service.nom')->label('Service'),
                    ])->columns(2),

                Section::make('Dates')
                    ->schema([
                        TextEntry::make('created_at')->label('Date de création')->dateTime(),
                        TextEntry::make('updated_at')->label('Date de modification')->dateTime(),
                    ])->columns(2),
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
