<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('changeStatus')
                ->label('Изменить статус')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->label('Новый статус')
                        ->options(\App\Models\Order::STATUSES)
                        ->default(fn() => $this->record->status)
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('comment')
                        ->label('Комментарий')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update(['status' => $data['status']]);
                    $this->record->statusLogs()->create([
                        'status'  => $data['status'],
                        'comment' => $data['comment'] ?? null,
                    ]);
                    \Filament\Notifications\Notification::make()->title('Статус обновлён')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            \Filament\Actions\Action::make('uploadFile')
                ->label('Загрузить файл')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    \Filament\Forms\Components\Select::make('type')
                        ->label('Тип')
                        ->options(['audio' => 'Аудио', 'cover' => 'Обложка'])
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('label')
                        ->label('Название (версия 1, 2...)'),
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Файл')
                        ->disk('public')
                        ->directory('order-files')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->files()->create([
                        'type'  => $data['type'],
                        'path'  => $data['file'],
                        'label' => $data['label'] ?? null,
                    ]);
                    \Filament\Notifications\Notification::make()->title('Файл загружен')->success()->send();
                }),
        ];
    }
}
