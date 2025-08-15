<?php

namespace App\Filament\Resources\FactureResource\Pages;

use App\Filament\Resources\FactureResource;
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

class ViewFacture extends ViewRecord
{
    protected static string $resource = FactureResource::class;

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
                Section::make('Informations sur la Facture')
                    ->schema([
                        TextEntry::make('numero_facture'),
                        TextEntry::make('date_facture')->date(),
                        TextEntry::make('echeance_payment')->date(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn ($record) => $record->status->getFilamentBadge()),
                    ])->columns(2),

                Section::make('Informations sur le Contrat')
                    ->schema([
                        TextEntry::make('contrat.numero_contrat')
                            ->label('Numéro de contrat'),
                        TextEntry::make('contrat.client.nom')
                            ->label('Client'),
                    ])->columns(2),

                Section::make('Informations Financières')
                    ->schema([
                        TextEntry::make('montant_ht')
                            ->formatStateUsing(fn ($record): string => $record->montant_ht . ' ' . $record->contrat->devise)
                            ->label('Montant hors taxe'),
                        TextEntry::make('montnat_ttc')
                            ->formatStateUsing(fn ($record): string => $record->montnat_ttc . ' ' . $record->contrat->devise)
                            ->label('Montant TTC'),
                        TextEntry::make('tva')
                            ->formatStateUsing(fn ($state): string => $state . ' %')
                            ->label('TVA'),
                    ])->columns(3),

                Section::make('Pièces Jointes')
                    ->schema([
                        RepeatableEntry::make('piecesJointes')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('nom_fichier')
                                    ->label('Nom du fichier')
                                    ->icon('heroicon-o-document'),
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
                                            return Storage::disk('public')->download($record->chemin_fichier, $record->nom_fichier);
                                        }),
                                    InfolistAction::make('view')
                                        ->label('Voir')
                                        ->icon('heroicon-o-eye')
                                        ->color('primary')
                                        ->url(fn (PieceJointe $record): string => Storage::disk('public')->url($record->chemin_fichier))
                                        ->openUrlInNewTab(),
                                ]),
                            ])
                            ->columns(2),
                    ])
                    ->collapsible(),
            ]);
    }
}
