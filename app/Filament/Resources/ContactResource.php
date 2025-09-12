<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use App\Models\Pays;
use App\Models\BusinessUnit;
use App\Models\User;
use App\Traits\HasActiveIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Notification;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResourceExported;
use Filament\Notifications\Notification as FilamentNotification;

class ContactResource extends Resource
{
    use HasActiveIcon;
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'lucide-contact';
    protected static ?string $navigationActiveIcon = 'bxs-contact';
    protected static ?string $recordTitleAttribute = 'nom';
    protected static ?string $navigationGroup = 'CRM';


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
                Forms\Components\Section::make('Informations Générales')
                    ->schema([
                        Forms\Components\FileUpload::make('profile_picture')
                            ->label('Photo de Profil')
                            ->image()
                            ->disk('public')
                            ->directory('profile')
                            ->visibility('public')
                            ->nullable()
                            ->maxSize(1024) // 1MB
                            ->acceptedFileTypes(['image/*'])
                            ->avatar()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('advanced_mode')
                            ->dehydrated(false)
                            ->label('Mode Avancé')
                            ->default(false)
                            ->reactive(),
                        Forms\Components\Select::make('title')
                            ->label('Titre')
                            ->options([
                                'Mr' => 'Mr',
                                'Mrs' => 'Mrs',
                                'Ms' => 'Ms',
                                'Dr' => 'Dr',
                                'Prof' => 'Prof',
                            ])
                            ->default('Mr')
                            ->hidden(fn (Get $get): bool => !$get('advanced_mode'))
                            ->nullable()
                            ->required(),
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
                            ->label('Type de Contact')
                            ->options([
                                'prospect' => 'Prospect',
                                'client' => 'Client',
                                'partner' => 'Partenaire',
                                'fournisseur' => 'Fournisseur',
                            ])
                            ->required(),
                        Forms\Components\Radio::make('company_type')
                            ->label('Type de Société')
                            ->options([
                                'individual' => 'Individuel',
                                'corporate' => 'Société',
                            ])
                            ->inline()
                            ->default('individual')
                            ->required()
                            ->live()
                            ->hidden(fn (Get $get): bool => !$get('advanced_mode')),
                        // Nom de la société
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nom de la Société')
                            ->required(fn (Get $get): bool => $get('company_type') === 'corporate')
                            ->hidden(fn (Get $get): bool => !$get('advanced_mode') || $get('company_type') === 'individual')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website')
                            ->label('Site Web')
                            ->url()
                            ->hidden(fn (Get $get): bool => !$get('advanced_mode') || $get('company_type') === 'individual')
                            ->nullable()
                            ->maxLength(255),
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
                        // adresse
                        Forms\Components\TextInput::make('adresse')
                            ->label('Adresse')
                            ->nullable()
                            ->maxLength(255),
                    ])->columns(3),

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
                Tables\Columns\ImageColumn::make('profile_picture')
                    ->getStateUsing(fn ($record) => $record->profile_picture
                        ? url('storage/' . $record->profile_picture)
                        : null)
                    ->label('Photo de Profil')
                    ->size(50)
                    ->circular()
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=Contact&background=random&color=fff'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->default(fn ($record) => $record->title ?? 'N/A')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nom')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('prenom')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'prospect' => 'info',
                        'client' => 'success',
                        'partner' => 'warning',
                        'fournisseur' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('company_type')
                    ->label('Type Société')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'individual' => 'success', // vert
                        'corporate'    => 'info',    // bleu
                        default      => 'secondary', // gris par défaut
                    })
                    ->sortable()
                    ->toggleable(),


                Tables\Columns\TextColumn::make('company_name')
                    ->label('Nom Société')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('website')
                    ->label('Site Web')
                    ->url(fn ($record) => $record->website, true)
                    ->openUrlInNewTab()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('adresse')
                    ->label('Adresse')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ville.nom')
                    ->label('Ville')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ville.pays.nom')
                    ->label('Pays')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('specialite.nom')
                    ->label('Spécialité')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('businessUnit.nom')
                    ->label('Unité Commerciale')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('service.nom')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('title')
                    ->options([
                        'Mr' => 'Mr',
                        'Mrs' => 'Mrs',
                        'Ms' => 'Ms',
                        'Dr' => 'Dr',
                        'Prof' => 'Prof',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'prospect' => 'Prospect',
                        'client' => 'Client',
                        'partner' => 'Partenaire',
                        'fournisseur' => 'Fournisseur',
                    ]),

                Tables\Filters\SelectFilter::make('company_type')
                    ->options([
                        'individual' => 'Individuel',
                        'corporate' => 'Société',
                    ])
                    ->label('Type Société'),

                Tables\Filters\Filter::make('company_name')
                    ->form([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nom Société'),
                    ])
                    ->query(fn ($query, array $data) => $query->when($data['company_name'], fn ($q, $value) => $q->where('company_name', 'like', "%{$value}%"))),

                Tables\Filters\Filter::make('website')
                    ->form([
                        Forms\Components\TextInput::make('website')
                            ->label('Site Web'),
                    ])
                    ->query(fn ($query, array $data) => $query->when($data['website'], fn ($q, $value) => $q->where('website', 'like', "%{$value}%"))),

                Tables\Filters\SelectFilter::make('ville')
                    ->relationship('ville', 'nom'),

                Tables\Filters\SelectFilter::make('specialite')
                    ->relationship('specialite', 'nom'),

                Tables\Filters\SelectFilter::make('businessUnit')
                    ->relationship('businessUnit', 'nom'),

                Tables\Filters\SelectFilter::make('service')
                    ->relationship('service', 'nom'),
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
                    ExportBulkAction::make()
                        ->visible(auth()->user()->can('export_contact'))
                        ->after(function () {
                            Notification::send(
                                User::admins(),
                                FilamentNotification::make()
                                    ->title('Contacts a été exporté par ' . auth()->user()->name)
                                    ->info()
                                    ->body('L\'utilisateur ' . auth()->user()->name . ' a exporté la resource Contacts')
                                    ->actions([
                                        Action::make('voir+')
                                            ->url(route('filament.admin.resources.users.view', auth()->id()))
                                            ->icon('heroicon-o-eye')
                                    ])
                                    ->toDatabase()
                            );
                        })

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
