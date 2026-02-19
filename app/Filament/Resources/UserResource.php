<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Пользователи';
    protected static ?string $modelLabel = 'Пользователь';
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Имя')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\IconColumn::make('is_admin')->label('Админ')->boolean(),
                Tables\Columns\TextColumn::make('orders_count')->label('Заказов')
                    ->counts('orders'),
                Tables\Columns\TextColumn::make('created_at')->label('Зарегистрирован')
                    ->dateTime('d.m.Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('toggleAdmin')
                    ->label(fn(User $record) => $record->is_admin ? 'Снять права' : 'Сделать админом')
                    ->icon('heroicon-o-shield-check')
                    ->requiresConfirmation()
                    ->action(fn(User $record) => $record->update(['is_admin' => !$record->is_admin])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
