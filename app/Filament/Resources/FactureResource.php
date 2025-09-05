<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FactureResource\Pages;
use App\Filament\Resources\FactureResource\RelationManagers;
use App\Models\Facture;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Traits\HasActiveIcon;
use App\Enums\FactureStatus;
use Filament\Tables\Actions\ActionGroup;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class FactureResource extends Resource
{
    use HasActiveIcon;
    protected static ?string $model = Facture::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationActiveIcon = 'heroicon-s-document-text';
    protected static ?string $navigationGroup = 'Gestion Commerciale';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('numero_facture')
                    ->placeholder('Optionel, généré automatiquement'),
                Forms\Components\DatePicker::make('date_facture'),
                Forms\Components\DatePicker::make('echeance_payment')
                    ->label('Echeance paiement'),
                Forms\Components\TextInput::make('montant_ht')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tva')
                    ->required()
                    ->numeric()
                    ->default(20),
                Forms\Components\Select::make('devise')
                    ->options([
                        'MAD' => 'MAD',
                        'EUR' => 'EUR',
                        'USD' => 'USD',
                    ])
                    ->required()
                    ->default('EUR'),
                Forms\Components\Select::make('status')
                    ->options(FactureStatus::class)
                    ->required(),
                Forms\Components\Select::make('contrat_id')
                    ->relationship('contrat', 'numero_contrat')
                    ->required()
                    ->preload()
                    ->label('Numéro de Contrat')
                    ->searchable(),
                Forms\Components\Section::make('Pièces Jointes')
                    ->schema([
                        Forms\Components\Repeater::make('piecesJointes')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nom_fichier')
                                    ->required(),
                                Forms\Components\FileUpload::make('chemin_fichier')
                                    ->directory('facture')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->downloadable()
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_facture')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_facture')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('echeance_payment')
                    ->label('Echeance paimenet')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_ht')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_ttc')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('devise')
                    ->label('Devise')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tva')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (Facture $record): string => $record->status->getFilamentBadge())
                    ->searchable(),
                Tables\Columns\TextColumn::make('contrat_id')
                    ->getStateUsing(fn ($record) => $record->contrat->numero_contrat)
                    ->numeric()
                    ->label('Numéro de Contrat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()

                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFactures::route('/'),
            'create' => Pages\CreateFacture::route('/create'),
            'edit' => Pages\EditFacture::route('/{record}/edit'),
            'view' => Pages\ViewFacture::route('/{record}')
        ];
    }
}
