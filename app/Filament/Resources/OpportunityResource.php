<?php

namespace App\Filament\Resources;

use App\Enums\OpportunityStatut;
use App\Filament\Resources\OpportunityResource\Pages;
use App\Models\Opportunity;
use App\Models\Pipeline;
use App\Traits\HasActiveIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use PhpOption\Option;

class OpportunityResource extends Resource
{
    use HasActiveIcon;

    protected static ?string $model = Opportunity::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?string $navigationActiveIcon = 'heroicon-o-light-bulb';
    protected static ?string $navigationGroup = 'CRM';

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
                    ->options(OpportunityStatut::class)
                    ->required()
                    ->live()
                    ->default(OpportunityStatut::OPEN),
                Forms\Components\TextInput::make('montant_reel')
                    ->label('Montant Réel')
                    ->hidden(function (Get $get) {
                        return $get('status') != OpportunityStatut::WON->value;
                    })
                    ->numeric(),
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
                    ->required()
                    ->label('Pipeline')
                    ->options(Pipeline::all()->pluck('nom', 'id'))
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('etape_pipeline_id', null))
                    ->nullable(),
                Forms\Components\Select::make('etape_pipeline_id') ->label('Étape du Pipeline')
                    ->required()
                    ->options(fn (Get $get): array => Pipeline::find($get('pipeline_id'))?->etapePipelines->pluck('nom', 'id')->toArray() ?? []),


                Forms\Components\Section::make('Pièces Jointes')
                    ->schema([
                        Forms\Components\Repeater::make('piecesJointes')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nom_fichier')
                                    ->required(),
                                Forms\Components\FileUpload::make('chemin_fichier')
                                    ->directory('opportunity')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->downloadable()
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations Générales')
                    ->schema([
                        TextEntry::make('titre'),
                        TextEntry::make('identifiant')
                            ->getStateUsing(fn (Opportunity $record): string => "{$record->prefix}-{$record->id}"),
                        TextEntry::make('description'),
                        TextEntry::make('note')->markdown(),
                        TextEntry::make('date_echeance')
                            ->date(),
                        TextEntry::make('probabilite')
                            ->suffix('%'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (Opportunity $record): string => $record->status->getBadge()),
                    ])->columns(2),
                Section::make('Informations Financières')
                    ->schema([
                        TextEntry::make('montant_estime')
                            ->label('Montant Potentiel')
                            ->money('MAD'),
                        TextEntry::make('devise'),
                    ])->columns(2),
                Section::make('Détails du Contact et Source')
                    ->schema([
                        TextEntry::make('contact.nom')
                            ->label('Nom du Contact'),
                        TextEntry::make('contact.prenom')
                            ->label('Prénom du Contact'),
                        TextEntry::make('contact.email')
                            ->label('Email du Contact'),
                        TextEntry::make('contact.telephone')
                            ->label('Téléphone du Contact'),
                        TextEntry::make('contact.type')
                            ->label('Type de Contact')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'prospect' => 'info',
                                'client' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('source.nom')
                            ->label('Source'),
                        TextEntry::make('contact.businessUnit.nom')
                            ->label('Unité Commerciale'),
                        TextEntry::make('contact.service.nom')
                            ->label('Service'),
                    ])->columns(2),
                Section::make('Pipeline et Étape')
                    ->schema([
                        TextEntry::make('pipeline.nom')
                            ->label('Pipeline'),
                        TextEntry::make('etapePipeline.nom')
                            ->label('Étape Actuelle'),
                    ])->columns(2),
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
                    ->formatStateUsing(fn (string $state, Opportunity $record): string => $state != null ? "{$state} {$record->devise}" : "Non précisé"),
                Tables\Columns\TextColumn::make('montant_reel')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (string $state, Opportunity $record): string => $state != null && $state !== '' ? "{$state} {$record->devise}" : "Non précisé"),
                Tables\Columns\TextColumn::make('date_echeance')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('probabilite')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn (OpportunityStatut $state): string => $state->getLabel())
                    ->color(fn (OpportunityStatut $state): string => $state->getTailwindBadge()),
                Tables\Columns\TextColumn::make('contact_info')
                    ->label('Contact')
                    ->getStateUsing(fn (\App\Models\Opportunity $record): string => "{$record->contact->nom} {$record->contact->prenom}")
                    ->searchable(['contact.nom', 'contact.prenom', 'contact.type'])
                    ->sortable(['contact.nom'])
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('contact.type')
                    ->label('Type de Contact')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'prospect' => 'info',
                        'client' => 'success',
                        default => 'gray',
                    })
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
            Tables\Filters\SelectFilter::make('contact')
                    ->relationship('contact', 'nom'),
            Tables\Filters\SelectFilter::make('source')
                    ->relationship('source', 'nom'),
            Tables\Filters\SelectFilter::make('pipeline')
                    ->relationship('pipeline', 'nom'),
            Tables\Filters\SelectFilter::make('etapePipeline')
                    ->relationship('etapePipeline', 'nom'),
            Tables\Filters\SelectFilter::make('contact_type')
                    ->relationship('contact', 'type')
                    ->label('Type de Contact')
                    ->options([
                        'prospect' => 'Prospect',
                        'client' => 'Client',
                    ]),

            Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(OpportunityStatut::cases())->mapWithKeys(
                        fn (OpportunityStatut $status) => [$status?->value => $status->getLabel()]
                    ))
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value'])) {
                            $query->where('status', $data['value']);
                        }
                        return $query;
                    }),
        ], layout: FiltersLayout::AboveContentCollapsible)
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
            'view' => Pages\ViewOpportunityDetails::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
