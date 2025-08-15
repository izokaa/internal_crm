<?php

namespace App\Filament\Resources;

use App\Enums\DevisStatus;
use App\Filament\Resources\DevisResource\Pages;
use App\Filament\Resources\DevisResource\RelationManagers;
use App\Models\Devis;
use App\Traits\HasActiveIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DevisResource extends Resource
{
    use HasActiveIcon;

    protected static ?string $model = Devis::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationActiveIcon = 'heroicon-s-document-currency-dollar';
    protected static ?string $navigationGroup = 'Gestion Commerciale';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('total_ht')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tva')
                    ->numeric()
                    ->default(20),
                Forms\Components\TextInput::make('remise')
                    ->required()
                    ->numeric()
                    ->suffix('%')
                    ->default(0),
                Forms\Components\Select::make('devise')
                    ->options([
                        'MAD' => 'MAD',
                        'EUR' => 'EUR',
                        'USD' => 'USD',
                    ])
                    ->required()
                    ->default('EUR'),
                Forms\Components\DatePicker::make('date_emission'),
                Forms\Components\DatePicker::make('date_devis')
                ->default(now()) ,
                Forms\Components\TextInput::make('validity_duration')
                    ->required()
                    ->numeric()
                    ->default(30),
                Forms\Components\Select::make('status')
                    ->default(DevisStatus::DRAFT)
                    ->options(DevisStatus::class)
                    ->required(),
                Forms\Components\Textarea::make('note')
                    ->columnSpanFull(),
                Forms\Components\Select::make('contact_id')
                    ->relationship('contact')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nom . $record->prenom)
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Contact'),
                Forms\Components\Section::make('Pièces Jointes')
                    ->schema([
                        Forms\Components\Repeater::make('piecesJointes')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nom_fichier')
                                    ->required(),
                                Forms\Components\FileUpload::make('chemin_fichier')
                                    ->directory('devis')
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
                Tables\Columns\TextColumn::make('quote_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_ht')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_ttc')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tva')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remise')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('devise')
                    ->label('Devise')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('date_emission')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_devis')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('validity_duration')
                    ->label('Durée de validité (jours)')
                    ->default(30)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_id')
                    ->label('Contact ID')
                    ->searchable()
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
            'index' => Pages\ListDevis::route('/'),
            'create' => Pages\CreateDevis::route('/create'),
            'edit' => Pages\EditDevis::route('/{record}/edit'),
        ];
    }
}
