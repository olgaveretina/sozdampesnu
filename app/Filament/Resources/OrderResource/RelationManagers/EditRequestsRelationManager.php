<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class EditRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'editRequests';
    protected static ?string $title = 'Заявки на правку';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('instructions')
                    ->label('Инструкция клиента')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending_payment' => 'Ожидает оплаты',
                        'paid'            => 'Оплачена',
                        'canceled'        => 'Отменена',
                        default           => $state,
                    })
                    ->colors([
                        'gray'    => 'pending_payment',
                        'success' => 'paid',
                        'danger'  => 'canceled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([])
            ->bulkActions([]);
    }
}
