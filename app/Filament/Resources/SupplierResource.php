<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(string $operation, $state, Set $set) => $operation
                                        === 'create' ? $set('slug', Str::slug($state)) : null),

                                TextInput::make('slug')
                                    ->maxLength(255)
                                    ->disabled()
                                    ->required()
                                    ->dehydrated()
                                    ->unique(Supplier::class, 'slug', ignoreRecord: true), // Fixed: Changed from FundClass to Supplier

                                TextInput::make('tin')
                                    ->label('TIN')
                                    ->maxLength(255)
                                    ->placeholder('123-456-789-000'),

                                Toggle::make('is_active')
                                    ->required()
                                    ->default(true)
                            ]),
                    ]),

                Section::make('Contact Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('supplier@example.com'),

                                TextInput::make('contact_person')
                                    ->maxLength(255)
                                    ->placeholder('John Doe'),

                                TextInput::make('mobile')
                                    ->label('Mobile Number')
                                    ->tel()
                                    ->maxLength(255)
                                    ->placeholder('+63 912 345 6789'),

                                TextInput::make('telephone')
                                    ->label('Telephone Number')
                                    ->tel()
                                    ->maxLength(255)
                                    ->placeholder('(033) 123-4567'),
                            ]),

                        Textarea::make('address')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Complete address'),

                        Textarea::make('remarks')
                            ->maxLength(1000)
                            ->rows(3)
                            ->placeholder('Additional notes or remarks'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->label('Mobile')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
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
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
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
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
