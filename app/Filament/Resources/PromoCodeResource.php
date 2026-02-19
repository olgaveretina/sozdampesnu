<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Промокоды';
    protected static ?string $modelLabel = 'Промокод';
    protected static ?string $pluralModelLabel = 'Промокоды';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->label('Код')
                ->required()
                ->maxLength(50)
                ->afterStateUpdated(fn($set, $state) => $set('code', strtoupper($state)))
                ->live(debounce: 300),
            Forms\Components\TextInput::make('discount_percent')
                ->label('Скидка (%)')
                ->numeric()
                ->minValue(1)
                ->maxValue(100)
                ->required(),
            Forms\Components\TextInput::make('max_uses')
                ->label('Макс. использований (пусто = неограничено)')
                ->numeric()
                ->minValue(1),
            Forms\Components\Toggle::make('is_active')
                ->label('Активен')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Код')->searchable(),
                Tables\Columns\TextColumn::make('discount_percent')->label('Скидка')->suffix('%'),
                Tables\Columns\TextColumn::make('used_count')->label('Использован'),
                Tables\Columns\TextColumn::make('max_uses')->label('Лимит')
                    ->formatStateUsing(fn($state) => $state ?? '∞'),
                Tables\Columns\IconColumn::make('is_active')->label('Активен')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Создан')->dateTime('d.m.Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit'   => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
