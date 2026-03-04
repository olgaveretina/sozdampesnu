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
                Forms\Components\TextInput::make('song_name')->label('Название')->disabled(),
                Forms\Components\Select::make('plan')->label('Тариф')
                    ->options(collect(Order::plans())->map(fn($p) => $p['name']))->disabled(),
                Forms\Components\TextInput::make('order_type')->label('Тип заказа')
                    ->formatStateUsing(fn($state) => Order::TYPES[$state] ?? $state)->disabled(),
                Forms\Components\Select::make('status')->label('Статус')
                    ->options(Order::STATUSES)->disabled(),
                Forms\Components\TextInput::make('amount_paid')->label('Оплачено (₽)')->disabled(),
            ])->columns(2),

            // Song-only fields (plans 1 & 2)
            Forms\Components\Section::make('Текст и пожелания')->schema([
                Forms\Components\Textarea::make('lyrics')->label('Текст песни')->disabled()->rows(8)->columnSpanFull(),
                Forms\Components\Textarea::make('music_style')->label('Стиль музыки')->disabled()->rows(3),
                Forms\Components\TextInput::make('lyrics_edit_permission')
                    ->label('Изменение текста')
                    ->disabled()
                    ->formatStateUsing(fn($state) => match($state) {
                        'none'  => 'Ничего менять нельзя',
                        'minor' => 'Только незначительные изменения',
                        'any'   => 'Любые изменения на наше усмотрение',
                        default => $state,
                    }),
            ])->visible(fn($livewire) => ($livewire->record?->order_type ?? 'song') !== 'video'),

            // Video-only fields (plan 3)
            Forms\Components\Section::make('Материалы видеоклипа')->schema([
                Forms\Components\Textarea::make('singer_description')
                    ->label('Описание исполнителя / персонажа')->disabled()->rows(3),
                Forms\Components\Textarea::make('cover_description')
                    ->label('Описание видеоклипа')->disabled()->rows(4)->columnSpanFull(),
                Forms\Components\Placeholder::make('video_audio_link')
                    ->label('Аудио файл')
                    ->content(fn($livewire) => $livewire->record?->video_audio_path
                        ? new \Illuminate\Support\HtmlString(
                            '<a href="' . Storage::url($livewire->record->video_audio_path) . '" target="_blank" class="text-primary-600 underline">Открыть / скачать</a>'
                        )
                        : new \Illuminate\Support\HtmlString('<span class="text-gray-400">Не загружено</span>')
                    ),
                Forms\Components\Placeholder::make('video_images_display')
                    ->label('Фото исполнителя / сцены')
                    ->content(function ($livewire) {
                        $images = $livewire->record?->video_images ?? [];
                        if (empty($images)) {
                            return new \Illuminate\Support\HtmlString('<span class="text-gray-400">Не загружено</span>');
                        }
                        $html = '<div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:4px;">';
                        foreach ($images as $path) {
                            $url = Storage::url($path);
                            $html .= '<a href="' . $url . '" target="_blank">'
                                . '<img src="' . $url . '" style="height:100px;width:100px;object-fit:cover;border-radius:6px;" alt="Фото">'
                                . '</a>';
                        }
                        $html .= '</div>';
                        return new \Illuminate\Support\HtmlString($html);
                    })->columnSpanFull(),
            ])->visible(fn($livewire) => $livewire->record?->order_type === 'video'),

            // Shared
            Forms\Components\Section::make('Комментарий клиента')->schema([
                Forms\Components\Textarea::make('user_comment')->label('Комментарий')->disabled()->rows(3)->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('song_name')->label('Название')->searchable(),
                Tables\Columns\TextColumn::make('performer_name')->label('Исполнитель')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Клиент')->searchable(),
                Tables\Columns\TextColumn::make('plan')->label('Тариф')
                    ->formatStateUsing(fn($state) => Order::plans()[$state]['name'] ?? $state),
                Tables\Columns\BadgeColumn::make('order_type')->label('Тип')
                    ->formatStateUsing(fn($state) => Order::TYPES[$state] ?? $state)
                    ->colors(['primary' => 'song', 'warning' => 'video']),
                Tables\Columns\BadgeColumn::make('status')->label('Статус')
                    ->formatStateUsing(fn($state) => Order::STATUSES[$state] ?? $state)
                    ->colors([
                        'gray'    => fn($state) => in_array($state, ['pending_payment', 'canceled']),
                        'warning' => fn($state) => in_array($state, ['new', 'sent_for_revision']),
                        'primary' => fn($state) => in_array($state, ['in_progress', 'under_revision']),
                        'success' => fn($state) => in_array($state, ['generated', 'completed']),
                        'danger'  => fn($state) => in_array($state, ['rejected_by_distributor', 'rejected_by_platforms', 'rejected']),
                        'info'    => fn($state) => in_array($state, ['publication_queue', 'publishing', 'sent_to_distributor', 'approved_by_distributor']),
                    ]),
                Tables\Columns\TextColumn::make('amount_paid')->label('Сумма')->suffix(' ₽')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Дата')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('order_type')->label('Тип')
                    ->options(Order::TYPES),
                Tables\Filters\SelectFilter::make('status')->label('Статус')
                    ->options(Order::STATUSES),
                Tables\Filters\SelectFilter::make('plan')->label('Тариф')
                    ->options(collect(Order::plans())->map(fn($p) => $p['name'])),
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

                Tables\Actions\Action::make('rejectOrder')
                    ->label('Отклонить заказ')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('Отклонить заказ и вернуть деньги')
                    ->modalDescription('Заказ будет переведён в статус «Не сможем выполнить». Если оплата была получена — будет инициирован возврат полной суммы.')
                    ->modalSubmitActionLabel('Отклонить и вернуть деньги')
                    ->form([
                        Forms\Components\Textarea::make('comment')
                            ->label('Комментарий для клиента')
                            ->required()
                            ->rows(4)
                            ->placeholder('Объясните причину отказа...'),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $payment = $record->payment;
                        if ($payment && $payment->yookassa_id && $payment->status === 'succeeded' && $record->amount_paid > 0) {
                            try {
                                app(\App\Services\YooKassaService::class)->createRefund(
                                    $payment->yookassa_id,
                                    $record->amount_paid,
                                    'Возврат по заказу #' . $record->id . ': ' . $data['comment']
                                );
                                $payment->update(['status' => 'refunded']);
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Ошибка возврата через ЮKassa')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                                return;
                            }
                        }
                        $record->update(['status' => 'rejected']);
                        $record->statusLogs()->create([
                            'status'  => 'rejected',
                            'comment' => $data['comment'],
                        ]);
                        Notification::make()->title('Заказ отклонён, возврат инициирован')->success()->send();
                    })
                    ->hidden(fn(Order $record) => in_array($record->status, ['rejected', 'canceled', 'pending_payment', 'completed'])),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EditRequestsRelationManager::class,
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
