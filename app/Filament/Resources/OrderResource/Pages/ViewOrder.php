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

            \Filament\Actions\Action::make('rejectOrder')
                ->label('Отклонить заказ')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->modalHeading('Отклонить заказ и вернуть деньги')
                ->modalDescription('Заказ будет переведён в статус «Не сможем выполнить». Если оплата была получена — будет инициирован возврат полной суммы.')
                ->modalSubmitActionLabel('Отклонить и вернуть деньги')
                ->form([
                    \Filament\Forms\Components\Textarea::make('comment')
                        ->label('Комментарий для клиента')
                        ->required()
                        ->rows(4)
                        ->placeholder('Объясните причину отказа...'),
                ])
                ->action(function (array $data): void {
                    $payment = $this->record->payment;
                    if ($payment && $payment->yookassa_id && $payment->status === 'succeeded' && $this->record->amount_paid > 0) {
                        try {
                            app(\App\Services\YooKassaService::class)->createRefund(
                                $payment->yookassa_id,
                                $this->record->amount_paid,
                                'Возврат по заказу #' . $this->record->id . ': ' . $data['comment']
                            );
                            $payment->update(['status' => 'refunded']);
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Ошибка возврата через ЮKassa')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            return;
                        }
                    }
                    $this->record->update(['status' => 'rejected']);
                    $this->record->statusLogs()->create([
                        'status'  => 'rejected',
                        'comment' => $data['comment'],
                    ]);
                    \Filament\Notifications\Notification::make()->title('Заказ отклонён, возврат инициирован')->success()->send();
                    $this->refreshFormData(['status']);
                })
                ->hidden(fn() => in_array($this->record->status, ['rejected', 'canceled', 'pending_payment', 'completed'])),

            \Filament\Actions\Action::make('uploadFile')
                ->label('Загрузить файл')
                ->icon('heroicon-o-arrow-up-tray')
                ->fillForm(fn(): array => [
                    'label' => $this->record->song_name,
                ])
                ->form([
                    \Filament\Forms\Components\Select::make('type')
                        ->label('Тип')
                        ->options(['audio' => 'Аудио', 'cover' => 'Обложка'])
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('label')
                        ->label('Название'),
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Файл')
                        ->disk('public')
                        ->directory('order-files')
                        ->multiple()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $type = $data['type'];
                    $existingCount = $type === 'audio'
                        ? $this->record->audioFiles()->count()
                        : $this->record->coverFiles()->count();
                    foreach ((array) $data['file'] as $index => $path) {
                        $this->record->files()->create([
                            'type'  => $type,
                            'path'  => $path,
                            'label' => ($data['label'] ?? $this->record->song_name) . ' - версия ' . ($existingCount + $index + 1),
                        ]);
                    }
                    \Filament\Notifications\Notification::make()->title('Файлы загружены')->success()->send();
                }),
        ];
    }
}
