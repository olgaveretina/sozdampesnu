<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Order;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class StatusLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusLogs';
    protected static ?string $title = 'История статусов';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')->label('Статус')
                    ->formatStateUsing(fn($state) => Order::STATUSES[$state] ?? $state),
                Tables\Columns\TextColumn::make('comment')->label('Комментарий')->wrap(),
                Tables\Columns\TextColumn::make('created_at')->label('Дата')->dateTime('d.m.Y H:i'),
            ])
            ->defaultSort('created_at')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
