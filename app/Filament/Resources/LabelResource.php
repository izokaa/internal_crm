<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabelResource\Pages;
use App\Models\Label;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LabelResource extends Resource
{
    protected static ?string $model = Label::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Paramètres';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('value')
                                    ->required()
                                    ->maxLength(255),
                                ColorPicker::make('couleur')
                                    ->required(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                Toggle::make('for_task')
                                    ->label('Pour Tâche')
                                    ->helperText('Indique si ce label peut être utilisé pour les tâches.')
                                    ->default(false),
                                Toggle::make('for_event')
                                    ->label('Pour Événement')
                                    ->helperText('Indique si ce label peut être utilisé pour les événements.')
                                    ->default(false),
                                Toggle::make('for_call')
                                    ->label('Pour Tâche')
                                    ->helperText('Indique si ce label peut être utilisé pour les appels.')
                                    ->default(false),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('value')
                    ->searchable()
                    ->sortable(),
                ColorColumn::make('couleur'),
                IconColumn::make('for_task')
                    ->label('Pour Tâche')
                    ->boolean(),
                IconColumn::make('for_event')
                    ->label('Pour Événement')
                    ->boolean(),
                IconColumn::make('for_call')
                    ->label('Pour Appel')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                DeleteAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListLabels::route('/'),
            'create' => Pages\CreateLabel::route('/create'),
            'edit' => Pages\EditLabel::route('/{record}/edit'),
        ];
    }
}
