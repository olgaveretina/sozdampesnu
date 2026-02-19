<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\OrderFile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-musical-note';
    protected static ?string $navigationLabel = 'Заказы';
    protected static ?string $modelLabel = 'Заказ';
    protected static ?string $pluralModelLabel = 'Заказы';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Детали заказа')->schema([
                Forms\Components\TextInput::make('performer_name')->label('Исполнитель')->disabled(),
                Forms\Components\TextInput::make('song_name')->label('Название песни')->disabled(),
                Forms\Components\Select::make('plan')->label('Тариф')
                    ->options(collect(Order::PLANS)->map(fn($p) => $p['name']))->disabled(),
                Forms\Components\Select::make('status')->label('Статус')
                    ->options(Order::STATUSES)->disabled(),
                Forms\Components\TextInput::make('amount_paid')->label('Оплачено (₽)')->disabled(),
            ])->columns(2),

            Forms\Components\Section::make('Текст и пожелания')->schema([
                Forms\Components\Textarea::make('lyrics')->label('Текст песни')->disabled()->rows(8),
                Forms\Components\Textarea::make('music_style')->label('Стиль музыки')->disabled()->rows(3),
                Forms\Components\Textarea::make('cover_description')->label('Описание обложки')->disabled()->rows(3),
                Forms\Components\Textarea::make('user_comment')->label('Комментарий пользователя')->disabled()->rows(3),
            ]),

            Forms\Components\View::make('filament.forms.order-files')
                ->visibleOn('view'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('song_name')->label('Название песни')->searchable(),
                Tables\Columns\TextColumn::make('performer_name')->label('Исполнитель')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Клиент')->searchable(),
                Tables\Columns\TextColumn::make('plan')->label('Тариф')
                    ->formatStateUsing(fn($state) => Order::PLANS[$state]['name'] ?? $state),
                Tables\Columns\BadgeColumn::make('status')->label('Статус')
                    ->formatStateUsing(fn($state) => Order::STATUSES[$state] ?? $state)
                    ->colors([
                        'gray'    => fn($state) => in_array($state, ['pending_payment', 'canceled']),
                        'warning' => fn($state) => in_array($state, ['new', 'sent_for_revision']),
                        'primary' => fn($state) => in_array($state, ['in_progress', 'under_revision']),
                        'success' => fn($state) => in_array($state, ['generated', 'completed']),
                        'danger'  => fn($state) => in_array($state, ['rejected_by_distributor', 'rejected_by_platforms']),
                        'info'    => fn($state) => in_array($state, ['publication_queue', 'publishing', 'sent_to_distributor', 'approved_by_distributor']),
                    ]),
                Tables\Columns\TextColumn::make('amount_paid')->label('Сумма')->suffix(' ₽')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Дата')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Статус')
                    ->options(Order::STATUSES),
                Tables\Filters\SelectFilter::make('plan')->label('Тариф')
                    ->options(collect(Order::PLANS)->map(fn($p) => $p['name'])),
            ])
            ->actions([
                Tables\Actions\Action::make('changeStatus')
                    ->label('Изменить статус')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Новый статус')
                            ->options(Order::STATUSES)
                            ->required(),
                        Forms\Components\Textarea::make('comment')
                            ->label('Комментарий (необязательно)')
                            ->rows(3),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                        $record->statusLogs()->create([
                            'status'  => $data['status'],
                            'comment' => $data['comment'] ?? null,
                        ]);
                        app(\App\Services\TelegramService::class)
                            ->notifyUserStatusChange($record, $data['status'], $data['comment'] ?? null);
                        Notification::make()->title('Статус обновлён')->success()->send();
                    }),

                Tables\Actions\Action::make('uploadFile')
                    ->label('Загрузить файл')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->fillForm(fn(Order $record): array => [
                        'label' => $record->song_name,
                    ])
                    ->form([
                        Forms\Components\Select::make('type')
                            ->label('Тип файла')
                            ->options(['audio' => 'Аудио', 'cover' => 'Обложка'])
                            ->required(),
                        Forms\Components\TextInput::make('label')
                            ->label('Название'),
                        Forms\Components\FileUpload::make('file')
                            ->label('Файл')
                            ->disk('public')
                            ->directory('order-files')
                            ->multiple()
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $type = $data['type'];
                        $existingCount = $type === 'audio'
                            ? $record->audioFiles()->count()
                            : $record->coverFiles()->count();
                        foreach ((array) $data['file'] as $index => $path) {
                            $record->files()->create([
                                'type'  => $type,
                                'path'  => $path,
                                'label' => ($data['label'] ?? $record->song_name) . ' - версия ' . ($existingCount + $index + 1),
                            ]);
                        }
                        Notification::make()->title('Файлы загружены')->success()->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ChatMessagesRelationManager::class,
            RelationManagers\StatusLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view'  => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
