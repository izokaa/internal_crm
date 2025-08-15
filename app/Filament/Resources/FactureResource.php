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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\HasActiveIcon;

class FactureResource extends Resource
{
    protected static ?string $model = Facture::class;

    use HasActiveIcon;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationActiveIcon = 'heroicon-s-document-text';
    protected static ?string $navigationGroup = 'Gestion Commerciale';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('numero_facture'),
                Forms\Components\DatePicker::make('date_facture'),
                Forms\Components\DatePicker::make('echeance_payment'),
                Forms\Components\TextInput::make('montant_ht')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('montnat_ttc')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tva')
                    ->required()
                    ->numeric()
                    ->default(20),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('contrat_id')
                    ->required()
                    ->numeric(),
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
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_ht')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('montnat_ttc')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tva')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contrat_id')
                    ->numeric()
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFactures::route('/'),
            'create' => Pages\CreateFacture::route('/create'),
            'edit' => Pages\EditFacture::route('/{record}/edit'),
        ];
    }
}
