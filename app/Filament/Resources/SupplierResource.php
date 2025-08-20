<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\ContactResource\Pages\CreateContact;
use App\Filament\Resources\ContactResource\Pages\ViewContact;
use App\Filament\Resources\ContactResource\Pages\EditContact;


class SupplierResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationLabel = 'Fournisseurs';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Achats & Dépenses';   
    // change the root path of navigation from Contacts/list to Suppliers/list
    protected static ?string $navigationRoot = 'fournisseurs';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => CreateContact::route('/create'),
            'edit' => EditContact::route('/{record}/edit'),
            'view' => ViewContact::route('/{record}'),
        ];
    }
}
