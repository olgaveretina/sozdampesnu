<x-mail::message>
# Статус вашего заказа изменён

Здравствуйте, {{ $order->user->name }}!

Статус вашего заказа **«{{ $order->song_name ?? $order->performer_name }}»** (№{{ $order->id }}) изменён.

**Новый статус:** {{ \App\Models\Order::STATUSES[$newStatus] ?? $newStatus }}

@if($comment)
**Комментарий:** {{ $comment }}

@endif
<x-mail::button :url="route('orders.show', $order)">
Открыть заказ
</x-mail::button>

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
