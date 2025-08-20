<?php

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use Filament\Actions as ActionsActions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use App\Models\PieceJointe;
use Filament\Notifications\Notification;

class ViewDevis extends ViewRecord
{
    protected static string $resource = DevisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionsActions\EditAction::make(),
            ActionsActions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations sur le Devis')
                    ->schema([
                        TextEntry::make('quote_number')
                            ->label('Numéro de devis'),
                        TextEntry::make('date_devis')
                            ->date()
                            ->label('Date du devis'),
                        TextEntry::make('date_emission')
                            ->date()
                            ->label('Date d\'émission'),
                        TextEntry::make('validity_duration')
                            ->label('Durée de validité (jours)'),
                        TextEntry::make('status')
                        ->color(fn ($record) => $record->status->getFilamentBadge())
                        ->badge(),
                    ])->columns(2),

                Section::make('Informations Client')
                    ->schema([
                        TextEntry::make('contact.nom')
                            ->label('Nom'),
                        TextEntry::make('contact.prenom')
                            ->label('Prénom'),
                        TextEntry::make('contact.ville.pays.nom')
                            ->label('Pays'),
                        TextEntry::make('contact.ville.nom')
                            ->label('Ville'),
                        TextEntry::make('contact.businessUnit.nom')
                            ->label('Business Unit'),
                        TextEntry::make('contact.service.nom')
                            ->label('Service'),
                    ])->columns(2),

                Section::make('Informations Financières')
                    ->schema([
                        TextEntry::make('total_ht')
                            ->formatStateUsing(fn ($record): string => match($record->devise) {
                                'EUR' => $record->total_ht . ' €',
                                'USD' => $record->total_ht . '$',
                                'MAD' => $record->total_ht . ' MAD',
                                default => $record->total_ht . ' ' . $record->devise
                            })
                            ->label('Montant hors taxe'),
                        TextEntry::make('total_ttc')
                             ->formatStateUsing(fn ($record): string => match($record->devise) {
                                 'EUR' => $record->total_ttc . ' €',
                                 'USD' => $record->total_ttc . '$',
                                 'MAD' => $record->total_ttc . ' MAD',
                                 default => $record->total_ttc . ' ' . $record->devise,
                             })
                            ->label('Montant TTC'),
                        TextEntry::make('tva')
                            ->formatStateUsing(fn ($state) => $state . ' %')
                            ->label('TVA'),
                        TextEntry::make('remise')
                            ->formatStateUsing(fn ($state) => $state . ' %')
                            ->label('Remise'),
                    ])->columns(4),

                Section::make('Note')
                    ->schema([
                        TextEntry::make('note')
                             ->markdown()
                             ->hiddenLabel(),
                    ])
                    ->collapsible(),

                Section::make('Pièces Jointes')
                    ->schema([
                        RepeatableEntry::make('piecesJointes')
                             ->hiddenLabel()
                             ->schema([
                                 TextEntry::make('nom_fichier')
                                     ->label('Nom du fichier')
                                     ->icon('heroicon-o-document')
                                     ->columnSpan(1),
                                 TextEntry::make('created_at')
                                     ->label('Date d\'ajout')
                                     ->dateTime()
                                     ->columnSpan(1),
                                 Actions::make([
                                     InfolistAction::make('download')
                                         ->label('Télécharger')
                                         ->icon('heroicon-o-arrow-down-tray')
                                         ->color('success')
                                         ->action(function (PieceJointe $record) {
                                             $filePath = Storage::disk('public')->path($record->chemin_fichier);

                                             if (!Storage::disk('public')->exists($record->chemin_fichier)) {
                                                 Notification::make()
                                                     ->title('Erreur')
                                                     ->body('Le fichier est introuvable.')
                                                     ->danger()
                                                     ->send();
                                                 return;
                                             }

                                             $originalExtension = pathinfo($record->chemin_fichier, PATHINFO_EXTENSION);
                                             $downloadName = $record->nom_fichier;

                                             if (!pathinfo($downloadName, PATHINFO_EXTENSION) && $originalExtension) {
                                                 $downloadName = $downloadName . '.' . $originalExtension;
                                             }

                                             $mimeType = Storage::disk('public')->mimeType($record->chemin_fichier);

                                             return response()->download($filePath, $downloadName, [
                                                 'Content-Type' => $mimeType,
                                             ]);
                                         }),
                                     InfolistAction::make('view')
                                         ->label('Voir')
                                         ->icon('heroicon-o-eye')
                                         ->color('primary')
                                         ->url(fn (PieceJointe $record): string => Storage::disk('public')->url($record->chemin_fichier))
                                         ->openUrlInNewTab(),
                                 ])->columnSpan(2),
                             ])
                             ->columns(4),
                    ])
                    ->collapsible(),
            ]);
    }
}
