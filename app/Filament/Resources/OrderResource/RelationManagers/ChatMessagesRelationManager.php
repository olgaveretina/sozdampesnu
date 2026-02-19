<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ChatMessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'chatMessages';
    protected static ?string $title = 'Чат с клиентом';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('body')
                ->label('Сообщение')
                ->required()
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('От')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-user')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('body')->label('Сообщение')->wrap(),
                Tables\Columns\TextColumn::make('created_at')->label('Время')->dateTime('d.m.Y H:i'),
            ])
            ->defaultSort('created_at')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Ответить клиенту')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['is_admin'] = true;
                        $data['user_id']  = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
