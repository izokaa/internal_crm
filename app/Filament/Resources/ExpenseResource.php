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
                Forms\Components\Select::make('contact_id')
            ->relationship('contact')
            ->placeholder('Sélectionner un contact')
            ->getOptionLabelFromRecordUsing(fn ($record) => $record->nom . ' ' . $record->prenom . ' - ' . $record->type)
            ->required()
            ->searchable()
            ->preload()
            ->label('Contact'),
                Forms\Components\Select::make('opportunity_id')
                    ->relationship('opportunity')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->prefix . ' - ' . $record->id . ' - ' . $record->title)
                    ->placeholder('Sélectionner une opportunité')
                    ->searchable()
                    ->preload()
                    ->label('Opportunité'),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact_id')
                ->label('Contact')
                    ->getStateUsing(fn ($record) => $record->contact ? $record->contact->nom . ' ' . $record->contact->prenom : 'N/A')
                    ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('opportunity_id')
                    ->label('Opportunité')
                    ->getStateUsing(fn ($record) => $record->opportunity ? $record->opportunity->prefix . ' - ' . $record->opportunity->id . ' - ' . $record->opportunity->title : 'N/A')
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
