<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContratResource\Pages;
use App\Filament\Resources\ContratResource\RelationManagers;
use App\Models\Contrat;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;

class ContratResource extends Resource
{
    protected static ?string $model = Contrat::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Clients & Abonnements';
    protected static ?int $navigationSort = 11;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date_contrat')
                    ->required(),
                Forms\Components\DatePicker::make('date_debut')
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
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'nom')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nom . $record->prenom)
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Client'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('date_contrat')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('date_debut')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('date_fin')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('periode_contrat')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('periode_unite')
                    ->label('Unité de Période')
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
                    ->label('Nom Client')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('client.prenom')
                    ->label('Prénom Client')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
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
            'index' => Pages\ListContrats::route('/'),
            'create' => Pages\CreateContrat::route('/create'),
            'edit' => Pages\EditContrat::route('/{record}/edit'),
        ];
    }
}
