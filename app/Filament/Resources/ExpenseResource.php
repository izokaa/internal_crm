<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\HasActiveIcon;
use App\Enums\ExpenseStatus;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;

class ExpenseResource extends Resource
{
    use HasActiveIcon;
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationActiveIcon = 'heroicon-s-credit-card';

    protected static ?string $navigationGroup = 'Achats & Dépenses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', modifyQueryUsing: fn (Builder $query) => $query->where('type', 'fournisseur'))
                    ->placeholder('Sélectionner un fournisseur')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nom . ' ' . $record->prenom)
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Fournisseur'),
                Forms\Components\Select::make('client_id')
                    ->relationship('client', modifyQueryUsing: fn (Builder $query) => $query->where('type', '!=', 'fournisseur'))
                    ->placeholder('Sélectionner un client ou un prospect')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nom . ' ' . $record->prenom)
                    ->searchable()
                    ->preload()
                    ->label('Client'),
                Forms\Components\TextInput::make('montant_ht')
                    ->label('Montant HT')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tva')
                    ->required()
                    ->label('TVA (%)')
                    ->default(20)
                    ->numeric(),
                Forms\Components\Select::make('devise')
                    ->options([
                        'MAD' => 'MAD',
                        'EUR' => 'EUR',
                        'USD' => 'USD',
                    ])
                    ->default('MAD')
                    ->label('Devise')
                    ->required(),
                Forms\Components\DatePicker::make('date_expense')
                    ->label('date de la dépense')
                    ->default(now())
                    ->required(),

                Forms\Components\Select::make('category_id')
                    ->relationship('category')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nom)
                    ->placeholder('Sélectionner une catégorie')
                    ->preload()
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->options(ExpenseStatus::class)
                    ->default(ExpenseStatus::DRAFT)
                    ->required(),
                Forms\Components\MarkdownEditor::make('description')
                    ->placeholder('Description de la dépense')
                    ->label('Description')
                    ->columnSpanFull()
                    ->nullable()
                ,
                Forms\Components\Section::make('Pièces Jointes')
                    ->schema([
                        Forms\Components\Repeater::make('piecesJointes')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nom_fichier')
                                    ->required(),
                                Forms\Components\FileUpload::make('chemin_fichier')
                                    ->directory('expense')
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
                Tables\Columns\TextColumn::make('supplier_id')
                    ->label('Fournisseur')
                    ->getStateUsing(fn ($record) => $record->supplier ? $record->supplier->nom . ' ' . $record->supplier->prenom : 'N/A')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_id')
                    ->label('Client/Prospect')
                    ->getStateUsing(fn ($record) => $record->client ? $record->client->nom . ' ' . $record->client->prenom : 'N/A')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_ht')
                    ->label('Montant HT')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_ttc')
                    ->label('Montant TTC')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tva')
                    ->label('TVA (%)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('devise')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_expense')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category_id')
                    ->label('Catégorie')
                    ->getStateUsing(fn ($record) => $record->category ? $record->category->nom : 'N/A')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($record) => $record->status->getBadge())
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
                SelectFilter::make('status')
                    ->options(ExpenseStatus::class)
                    ->multiple()
                    ->label('Statut'),
                SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'nom')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('supplier_id')
                    ->label('Fournisseur')
                    ->relationship('supplier', 'nom', fn (Builder $query) => $query->where('type', 'fournisseur'))
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('devise')
                    ->options([
                        'MAD' => 'MAD',
                        'EUR' => 'EUR',
                        'USD' => 'USD',
                    ])
                    ->multiple(),
                Filter::make('date_expense')
                    ->form([
                        DatePicker::make('date_from')->label('Date de dépense (début)'),
                        DatePicker::make('date_until')->label('Date de dépense (fin)'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_expense', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_expense', '<=', $date),
                            );
                    }),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
