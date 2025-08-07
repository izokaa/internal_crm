<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpportunityResource\Pages;
use App\Filament\Resources\OpportunityResource\RelationManagers;
use App\Models\Opportunity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titre')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('note')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('montant_estime')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('date_echeance')
                    ->required(),
                Forms\Components\TextInput::make('probabilite')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('brief')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'ouvert' => 'Ouvert',
                        'ferme' => 'Fermé',
                        'en retard' => 'En retard',
                        'annule' => 'Annulé',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('prefix')
                    ->required(),
                Forms\Components\Select::make('contact_id')
                    ->relationship('contact', 'nom')
                    ->required(),
                Forms\Components\Select::make('source_id')
                    ->relationship('source', 'nom')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('montant_estime')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_echeance')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('probabilite')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prefix')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact.nom')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source.nom')
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
            'index' => Pages\ListOpportunities::route('/'),
            'create' => Pages\CreateOpportunity::route('/create'),
            'edit' => Pages\EditOpportunity::route('/{record}/edit'),
        ];
    }
}