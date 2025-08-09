<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessUnitResource\Pages;
use App\Filament\Resources\BusinessUnitResource\RelationManagers;
use App\Models\BusinessUnit;
use App\Traits\HasActiveIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BusinessUnitResource extends Resource
{
    use HasActiveIcon;

    protected static ?string $model = BusinessUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationActiveIcon = 'heroicon-s-building-office-2';

    protected static ?string $navigationGroup = 'Paramètres > Business';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails de l\'unité commerciale')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Repeater::make('services')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nom')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(1)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->createItemButtonLabel('Ajouter un service')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['nom'] ?? null),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('services_count')
                    ->counts('services')
                    ->label('Nombre de Services')
                    ->sortable(),
                Tables\Columns\TextColumn::make('contacts_count')
                    ->counts('contacts')
                    ->label('Nombre de Contacts')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Date de modification')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBusinessUnits::route('/'),
            'create' => Pages\CreateBusinessUnit::route('/create'),
            'edit' => Pages\EditBusinessUnit::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
