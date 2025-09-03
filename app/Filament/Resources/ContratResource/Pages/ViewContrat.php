<?php

namespace App\Filament\Resources\ContratResource\Pages;

use App\Filament\Resources\ContratResource;
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

class ViewContrat extends ViewRecord
{
    protected static string $resource = ContratResource::class;

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
                Section::make('Informations Générales')
                    ->schema([
                        TextEntry::make('numero_contrat')
                            ->label('Numéro contrat'),
                        TextEntry::make('date_contrat')
                            ->date()
                            ->label('Date de contrat'),
                        TextEntry::make('date_debut')
                            ->date()
                            ->label('Date début d\'abonnement'),
                        TextEntry::make('date_fin')
                            ->date()
                            ->label('Date fin d\'abonnement'),
                        TextEntry::make('periode_contrat')
                            ->formatStateUsing(fn ($record) => $record->periode_contrat . ' ' . $record->periode_unite),
                        TextEntry::make('status')
                            ->label('Statut du Contrat')
                            ->badge()
                            ->color(fn ($record) => $record->status->getFilamentBadge()),
                        TextEntry::make('mode_payment')
                            ->label('Mode de Paiement')
                            ->badge()
                            ->color(fn ($record) => $record->mode_payment->getFilamentBadge()),
                    ])->columns(2),

                Section::make('Informations client')
                    ->schema([
                        TextEntry::make('client.nom')
                            ->label('Nom'),
                        TextEntry::make('client.prenom')
                            ->label('Prénom'),
                        TextEntry::make('client.ville.pays.nom')
                            ->label('Pays'),
                        TextEntry::make('client.ville.nom')
                            ->label('Ville'),
                        TextEntry::make('client.businessUnit.nom')
                            ->label('Business Unit'),
                        TextEntry::make('client.service.nom')
                            ->label('Service'),
                    ])->columns(2),
                Section::make('Informations financières')
                    ->schema([
                        TextEntry::make('montant_ht')
                            ->formatStateUsing(fn ($record): string => match ($record->devise) {
                                'EUR' => $record->montant_ht . ' €',
                                'USD' => $record->montant_ht . '$',
                                'MAD' => $record->montant_ht . ' MAD',
                                default => $record->montant_ht . ' ' . $record->devise,
                            })
                            ->label('Montant hors taxe'),
                        TextEntry::make('montant_ttc')
                            ->formatStateUsing(fn ($record): string => match ($record->devise) {
                                'EUR' => $record->montant_ttc . ' €',
                                'USD' => $record->montant_ttc . '$',
                                'MAD' => $record->montant_ttc . ' MAD',
                                default => $record->montant_ttc . ' ' . $record->devise,
                            })
                            ->label('Montant TTC'),
                        TextEntry::make('tva')->label('TVA'),
                    ])->columns(3),

                Section::make('Pièces jointes')
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

                                            // Récupérer l'extension du fichier original
                                            $originalExtension = pathinfo($record->chemin_fichier, PATHINFO_EXTENSION);

                                            // Construire le nom du fichier avec la bonne extension
                                            $downloadName = $record->nom_fichier;

                                            // Vérifier si le nom du fichier a déjà une extension
                                            if (!pathinfo($downloadName, PATHINFO_EXTENSION) && $originalExtension) {
                                                $downloadName = $downloadName . '.' . $originalExtension;
                                            }

                                            // Déterminer le type MIME correct
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
                            ->columns(2),
                    ])
                    ->collapsible(),

            ]);
    }
}
