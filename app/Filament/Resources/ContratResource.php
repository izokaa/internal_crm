<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContratResource\Pages;
use App\Models\Contrat;
use App\Traits\HasActiveIcon;
use App\Enums\ContratStatus;
use App\Enums\ModePayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ContratResource extends Resource
{
    use HasActiveIcon;
    protected static ?string $model = Contrat::class;

    protected static ?string $navigationIcon = 'clarity-contract-line';
    protected static ?string $navigationActiveIcon = 'clarity-contract-solid';
    protected static ?string $navigationGroup = 'CRM';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Générales')
                    ->schema([
                        Forms\Components\DatePicker::make('date_contrat')
                            ->required(),
                        Forms\Components\DatePicker::make('date_debut')
                            ->label('Date début abonnement')
                            ->required()
                            ->afterOrEqual('date_contrat')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($get('date_fin') && $state) {
                                    $startDate = \Carbon\Carbon::parse($state);
                                    $endDate = \Carbon\Carbon::parse($get('date_fin'));

                                    $diffInYears = $startDate->diffInYears($endDate);
                                    if ($startDate->copy()->addYears($diffInYears)->equalTo($endDate)) {
                                        $set('periode_contrat', $diffInYears);
                                        $set('periode_unite', 'années');
                                    } else {
                                        $diffInMonths = $startDate->diffInMonths($endDate);
                                        if ($startDate->copy()->addMonths($diffInMonths)->equalTo($endDate)) {
                                            $set('periode_contrat', $diffInMonths);
                                            $set('periode_unite', 'mois');
                                        } else {
                                            $diffInDays = $startDate->diffInDays($endDate);
                                            $set('periode_contrat', $diffInDays);
                                            $set('periode_unite', 'jours');
                                        }
                                    }
                                }
                            }),
                        Forms\Components\DatePicker::make('date_fin')
                            ->required()
                            ->label('Date fin abonnement')
                            ->afterOrEqual('date_debut')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($get('date_debut') && $state) {
                                    $startDate = \Carbon\Carbon::parse($get('date_debut'));
                                    $endDate = \Carbon\Carbon::parse($state);

                                    $diffInYears = $startDate->diffInYears($endDate);
                                    if ($startDate->copy()->addYears($diffInYears)->equalTo($endDate)) {
                                        $set('periode_contrat', $diffInYears);
                                        $set('periode_unite', 'années');
                                    } else {
                                        $diffInMonths = $startDate->diffInMonths($endDate);
                                        if ($startDate->copy()->addMonths($diffInMonths)->equalTo($endDate)) {
                                            $set('periode_contrat', $diffInMonths);
                                            $set('periode_unite', 'mois');
                                        } else {
                                            $diffInDays = $startDate->diffInDays($endDate);
                                            $set('periode_contrat', $diffInDays);
                                            $set('periode_unite', 'jours');
                                        }
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('periode_contrat')
                            ->required()
                            ->numeric(),
                        Forms\Components\Select::make('periode_unite')
                            ->options([
                                'jours' => 'Jours',
                                'mois' => 'Mois',
                                'années' => 'Années',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                $startDate = \Carbon\Carbon::parse($get('date_debut'));
                                $endDate = \Carbon\Carbon::parse($get('date_fin'));

                                if ($startDate && $endDate) {
                                    switch ($state) {
                                        case 'jours':
                                            $set('periode_contrat', $startDate->diffInDays($endDate));
                                            break;
                                        case 'mois':
                                            $set('periode_contrat', $startDate->diffInMonths($endDate));
                                            break;
                                        case 'années':
                                            $set('periode_contrat', $startDate->diffInYears($endDate));
                                            break;
                                    }
                                }
                            }),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->nom . ' ' . $record->prenom . ' - ' . $record->businessUnit->nom . ' - ' . $record->service->nom)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Client'),
                        Forms\Components\Select::make('status')
                            ->label('Statut du Contrat')
                            ->default(ContratStatus::ACTIVE->value)
                            ->options(ContratStatus::class)
                            ->required(),
                        Forms\Components\Select::make('mode_payment')
                            ->label('Mode de Paiement')
                            ->options(ModePayment::class)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Informations Financières')
                    ->schema([
                        Forms\Components\TextInput::make('montant_ht')
                            ->label('Montant hors taxe (HT)')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('tva')
                            ->label('TVA')
                            ->default(20)
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
                    ])->columns(3),

                Forms\Components\Section::make('Pièces Jointes')
                    ->schema([
                        Forms\Components\Repeater::make('piecesJointes')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nom_fichier')
                                    ->required(),
                                Forms\Components\FileUpload::make('chemin_fichier')
                                    ->directory('contrats')
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
                Tables\Columns\TextColumn::make('numero_contrat')
                    ->label('Numéro de contrat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_contrat')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('date_debut')
                    ->label('Date début abonnement')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('date_fin')
                    ->label('Date fin abonnement')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('periode_contrat')
                    ->getStateUsing(fn ($record) => $record->periode_contrat . ' ' . $record->periode_unite)
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('montant_ht')
                    ->label('montant hors taxe (HT)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('montant_ttc')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('devise')
                    ->label('Devise')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('tva')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn (\App\Models\Contrat $record): string => "{$record->client->nom} {$record->client->prenom}"),
                Tables\Columns\TextColumn::make('client.businessUnit.nom')
                    ->label('Business Unit')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('client.service.nom')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut du Contrat')
                    ->badge()
                    ->color(fn (Contrat $record) => $record->status->getFilamentBadge())
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('mode_payment')
                    ->label('Mode de Paiement')
                    ->badge()
                    ->color(fn (Contrat $record) => $record->mode_payment->getFilamentBadge())
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ContratStatus::class)
                    ->label('Statut du Contrat'),
                Tables\Filters\SelectFilter::make('mode_payment')
                    ->options(ModePayment::class)
                    ->label('Mode de Paiement'),
                Tables\Filters\SelectFilter::make('client_id')
                    ->relationship('client', 'nom')
                    ->label('Client'),
                Tables\Filters\SelectFilter::make('devise')
                    ->options([
                        'MAD' => 'MAD',
                        'EUR' => 'EUR',
                        'USD' => 'USD',
                    ])
                    ->label('Devise'),
                Filter::make('date_debut')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_debut'),
                                Forms\Components\DatePicker::make('date_fin'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_debut'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_debut', '>=', $date),
                            )
                            ->when(
                                $data['date_fin'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_fin', '<=', $date),
                            );
                    })->columnSpanFull(),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListContrats::route('/'),
            'create' => Pages\CreateContrat::route('/create'),
            'view' => Pages\ViewContrat::route('/{record}'),
            'edit' => Pages\EditContrat::route('/{record}/edit'),
        ];
    }
}
