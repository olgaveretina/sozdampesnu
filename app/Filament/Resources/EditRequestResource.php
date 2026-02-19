<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EditRequestResource\Pages;
use App\Models\EditRequest;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EditRequestResource extends Resource
{
    protected static ?string $model = EditRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Правки';
    protected static ?string $modelLabel = 'Правка';
    protected static ?string $pluralModelLabel = 'Правки';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('order.performer_name')->label('Заказ'),
                Tables\Columns\TextColumn::make('order.user.name')->label('Клиент'),
                Tables\Columns\TextColumn::make('instructions')->label('Инструкция')->limit(80)->wrap(),
                Tables\Columns\BadgeColumn::make('status')->label('Статус')
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending_payment' => 'Ожидает оплаты',
                        'paid'            => 'Оплачена',
                        'in_progress'     => 'В работе',
                        'completed'       => 'Выполнена',
                        'canceled'        => 'Отменена',
                        default           => $state,
                    })
                    ->colors([
                        'gray'    => 'pending_payment',
                        'warning' => 'paid',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger'  => 'canceled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Дата')->dateTime('d.m.Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('changeStatus')
                    ->label('Статус')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'paid'        => 'Оплачена',
                                'in_progress' => 'В работе',
                                'completed'   => 'Выполнена',
                            ])
                            ->required(),
                    ])
                    ->action(fn(EditRequest $record, array $data) => $record->update(['status' => $data['status']])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEditRequests::route('/'),
        ];
    }
}
