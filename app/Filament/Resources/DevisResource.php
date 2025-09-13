<?php

namespace App\Filament\Resources;

use App\Enums\DevisStatus;
use App\Filament\Resources\DevisResource\Pages;
use App\Filament\Resources\DevisResource\RelationManagers;
use App\Models\{Devis, User};
use App\Traits\HasActiveIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Notification;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Notifications\Notification as FilamentNotification;

class DevisResource extends Resource
{
    use HasActiveIcon;

    protected static ?string $model = Devis::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationActiveIcon = 'heroicon-s-document-currency-dollar';
    protected static ?string $navigationGroup = 'Gestion Commerciale';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations sur le Devis')
                    ->schema([
                        Forms\Components\DatePicker::make('date_devis')
                            ->label('Date du devis')
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('date_emission')
                            ->label('Date d\'émission'),
                        Forms\Components\TextInput::make('validity_duration')
                            ->label('Durée de validité (jours)')
                            ->required()
                            ->numeric()
                            ->default(30),
                        Forms\Components\Select::make('status')
                            ->default(DevisStatus::DRAFT)
                            ->options(DevisStatus::class)
                            ->required(),
                    ])->columns(4),
                Forms\Components\Section::make('Informations Financières')
                    ->schema([
                        Forms\Components\TextInput::make('total_ht')
                            ->label('Total HT')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('tva')
                            ->label('TVA (%)')
                            ->numeric()
                            ->default(20),
                        Forms\Components\TextInput::make('remise')
                            ->label('Remise (%)')
                            ->required()
                            ->numeric()
                            ->suffix('%')
                            ->default(0),
                        Forms\Components\Select::make('devise')
                            ->options([
                                'MAD' => 'MAD',
                                'EUR' => 'EUR',
                                'USD' => 'USD',
                            ])
                            ->required()
                            ->default('EUR'),
                    ])->columns(4),
                Forms\Components\Section::make('Client et Notes')
                    ->schema([
                        Forms\Components\Select::make('contact_id')
                            ->relationship('contact')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->contact->nom . ' ' . $record->contact->prenom . ' - BU: ' . $record->contact->businessUnit->nom . ' - Service: ' . $record->contact->service->nom)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Contact'),
                        Forms\Components\MarkdownEditor::make('note')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Pièces Jointes')
                    ->schema([
                        Forms\Components\Repeater::make('piecesJointes')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nom_fichier')
                                    ->required(),
                                Forms\Components\FileUpload::make('chemin_fichier')
                                    ->directory('devis')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->downloadable()
                                    ->required(),
                            ])
                            ->collapsible()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quote_number')
                    ->label('Numéro de Devis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact.nom')
                    ->label('Contact')
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->contact->nom . " " . $record->contact->prenom)
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.email')
                    ->label('Email du Contact')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_ht')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tva')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_ttc')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remise')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('devise')
                    ->label('Devise')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_devis')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_emission')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (Devis $record): string => $record->status->getFilamentBadge())
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options(DevisStatus::class),
                Tables\Filters\SelectFilter::make('contact_id')
                    ->relationship('contact', 'nom')
                    ->label('Contact'),
                Tables\Filters\SelectFilter::make('devise')
                    ->options([
                        'MAD' => 'MAD',
                        'EUR' => 'EUR',
                        'USD' => 'USD',
                    ])
                    ->label('Devise'),
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
                    ExportBulkAction::make()
                        ->visible(auth()->user()->can('export_devis'))
                        ->after(function () {
                            Notification::send(
                                User::admins(),
                                FilamentNotification::make()
                                    ->title('Devis a été exporté par ' . auth()->user()->name)
                                    ->body('L\'utilisateur ' . auth()->user()->name . " a exporté la resource Devis")
                                    ->actions([
                                        Action::make('voir+')
                                            ->url(route('filament.admin.resources.users.view', auth()->id()))
                                            ->icon('heroicon-o-eye')
                                    ])
                                    ->info()
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevis::route('/'),
            'create' => Pages\CreateDevis::route('/create'),
            'view' => Pages\ViewDevis::route('/{record}'),
            'edit' => Pages\EditDevis::route('/{record}/edit'),
        ];
    }
}
