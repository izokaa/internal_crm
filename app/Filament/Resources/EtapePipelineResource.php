<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EtapePipelineResource\Pages;
use App\Filament\Resources\EtapePipelineResource\RelationManagers;
use App\Models\EtapePipeline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EtapePipelineResource extends Resource
{
    protected static ?string $model = EtapePipeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Paramètres > Opportunités';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ordre')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\Select::make('pipeline_id')
                    ->relationship('pipeline', 'nom')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ordre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pipeline.nom')
                    ->searchable()
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
                Tables\Filters\SelectFilter::make('pipeline')
                    ->relationship('pipeline', 'nom'),
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
            'index' => Pages\ListEtapePipelines::route('/'),
            'create' => Pages\CreateEtapePipeline::route('/create'),
            'edit' => Pages\EditEtapePipeline::route('/{record}/edit'),
        ];
    }

}
