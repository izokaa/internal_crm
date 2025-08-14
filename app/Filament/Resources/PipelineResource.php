<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PipelineResource\Pages;
use App\Filament\Resources\PipelineResource\RelationManagers;
use App\Models\Pipeline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PipelineResource extends Resource
{
    protected static ?string $model = Pipeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Paramètres';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails du Pipeline')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Repeater::make('etapePipelines')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nom')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('ordre')
                                    ->numeric()
                                    ->required()
                                    ->default(1),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->createItemButtonLabel('Ajouter une étape')
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
                Tables\Columns\TextColumn::make('etape_pipelines_count')
                    ->counts('etapePipelines')
                    ->label('Nombre d\'étapes')
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
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\EtapePipelinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPipelines::route('/'),
            'create' => Pages\CreatePipeline::route('/create'),
            'edit' => Pages\EditPipeline::route('/{record}/edit'),
            'view' => Pages\ViewPipeline::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
