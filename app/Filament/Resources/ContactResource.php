<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use App\Models\Pays;
use App\Models\BusinessUnit;
use App\Traits\HasActiveIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\ActionGroup;

class ContactResource extends Resource
{
    use HasActiveIcon;
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'lucide-contact';
    protected static ?string $navigationActiveIcon = 'bxs-contact';
    protected static ?string $recordTitleAttribute = 'nom';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'nom',
            'prenom',
            'email'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Personnelles')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('prenom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telephone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'prospect' => 'Prospect',
                                'client' => 'Client',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Localisation')
                    ->schema([
                        Forms\Components\Select::make('pays_id')
                            ->label('Pays')
                            ->options(Pays::all()->pluck('nom', 'id'))
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('ville_id', null))
                            ->nullable(),
                        Forms\Components\Select::make('ville_id')
                            ->label('Ville')
                            ->options(fn (Get $get): array => Pays::find($get('pays_id'))?->villes->pluck('nom', 'id')->toArray() ?? [])
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Professionnel')
                    ->schema([
                        Forms\Components\Select::make('specialite_id')
                            ->relationship('specialite', 'nom')
                            ->required(),
                        Forms\Components\Select::make('business_unit_id')
                            ->label('Business Unit')
                            ->options(BusinessUnit::all()->pluck('nom', 'id'))
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('service_id', null))
                            ->nullable(),
                        Forms\Components\Select::make('service_id')
                            ->label('Service')
                            ->options(fn (Get $get): array => BusinessUnit::find($get('business_unit_id'))?->services->pluck('nom', 'id')->toArray() ?? [])
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('prenom')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'prospect' => 'info',
                        'client' => 'success',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('ville.nom')
                    ->label('Ville')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('ville.pays.nom')
                    ->label('Pays')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('specialite.nom')
                    ->label('Spécialité')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('businessUnit.nom')
                    ->label('Unité Commerciale')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('service.nom')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'prospect' => 'Prospect',
                        'client' => 'Client',
                    ]),
                Tables\Filters\SelectFilter::make('ville')
                    ->relationship('ville', 'nom'),
                Tables\Filters\SelectFilter::make('specialite')
                    ->relationship('specialite', 'nom'),
                Tables\Filters\SelectFilter::make('businessUnit')
                    ->relationship('businessUnit', 'nom'),
                Tables\Filters\SelectFilter::make('service')
                    ->relationship('service', 'nom'),
            ])
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
            'view' => Pages\ViewContact::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
