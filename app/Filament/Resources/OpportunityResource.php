<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpportunityResource\Pages;
use App\Filament\Resources\OpportunityResource\RelationManagers;
use App\Models\Opportunity;
use App\Models\Pipeline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;

class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?string $navigationGroup = 'Opportunités';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'titre';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'titre'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\MarkdownEditor::make('note')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('montant_estime')
                            ->label('Montant Potentiel')
                            ->required()
                            ->numeric(),
                        Forms\Components\Select::make('devise')
                            ->options([
                                'MAD' => 'MAD',
                                'EUR' => 'EUR',
                                'USD' => 'USD',
                            ])
                            ->required()
                            ->default('EUR'),
                    ]),
                Forms\Components\DatePicker::make('date_echeance')
                    ->required(),
                Forms\Components\TextInput::make('probabilite')
                    ->required()
                    ->numeric()
                    ->suffix('%'),
                Forms\Components\Select::make('status')
                    ->options([
                        'Ouverte' => 'Ouverte',
                        'Gagnée' => 'Gagnée',
                        'Perdue' => 'Perdue',
                        'En retard' => 'En retard',
                        'Annulée' => 'Annulée',
                        'Fermée' => 'Fermée',
                    ])
                    ->required()
                    ->default('Ouverte'),
                Forms\Components\TextInput::make('prefix')
                    ->required()
                    ->maxLength(255)
                    ->default('OPPO'),
                Forms\Components\Select::make('contact_id')
                    ->relationship('contact')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nom . $record->prenom)
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Contact'),
                Forms\Components\Select::make('source_id')
                    ->relationship('source', 'nom')
                    ->required(),
                Forms\Components\Select::make('pipeline_id')
                    ->label('Pipeline')
                    ->options(Pipeline::all()->pluck('nom', 'id'))
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('etape_pipeline_id', null))
                    ->nullable(),
                Forms\Components\Select::make('etape_pipeline_id')
                    ->label('Étape du Pipeline')
                    ->options(fn (Get $get): array => Pipeline::find($get('pipeline_id'))?->etapePipelines->pluck('nom', 'id')->toArray() ?? [])
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identifiant')
                    ->label('Identifiant')
                    ->getStateUsing(fn (Opportunity $record): string => "{$record->prefix}-{$record->id}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('titre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('montant_estime')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (string $state, Opportunity $record): string => "{$state} {$record->devise}"),
                Tables\Columns\TextColumn::make('date_echeance')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('probabilite')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ouverte' => 'info',
                        'Gagnée' => 'success',
                        'Perdue' => 'danger',
                        'En retard' => 'warning',
                        'Annulée' => 'gray',
                        'Fermée' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('contact_info')
                    ->label('Contact')
                    ->getStateUsing(fn (\App\Models\Opportunity $record): string => "{$record->contact->nom} {$record->contact->prenom}")
                    ->searchable(['contact.nom', 'contact.prenom', 'contact.type'])
                    ->sortable(['contact.nom'])
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('source.nom')
                    ->label('Source')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('pipeline.nom')
                    ->label('Pipeline')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('etapePipeline.nom')
                    ->label('Étape Pipeline')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Date de modification')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Ouverte' => 'Ouverte',
                        'Gagnée' => 'Gagnée',
                        'Perdue' => 'Perdue',
                        'En retard' => 'En retard',
                        'Annulée' => 'Annulée',
                        'Fermée' => 'Fermée',
                    ]),
                Tables\Filters\SelectFilter::make('contact')
                    ->relationship('contact', 'nom'),
                Tables\Filters\SelectFilter::make('source')
                    ->relationship('source', 'nom'),
                Tables\Filters\SelectFilter::make('pipeline')
                    ->relationship('pipeline', 'nom'),
                Tables\Filters\SelectFilter::make('etapePipeline')
                    ->relationship('etapePipeline', 'nom'),
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
            'index' => Pages\ListOpportunities::route('/'),
            'create' => Pages\CreateOpportunity::route('/create'),
            'edit' => Pages\EditOpportunity::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


}
