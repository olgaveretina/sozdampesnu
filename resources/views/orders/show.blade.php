@extends('layouts.app')

@section('title', 'Заказ — ' . $order->performer_name)

@section('content')
<div class="row g-4">
    <div class="col-lg-8">

        {{-- Order header --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-1">{{ $order->performer_name }}</h4>
                        <span class="text-muted small">{{ $order->planLabel() }} · {{ $order->created_at->format('d.m.Y') }}</span>
                    </div>
                    <span class="badge bg-primary fs-6">{{ $order->statusLabel() }}</span>
                </div>
            </div>
        </div>

        {{-- Audio files --}}
        @if($order->audioFiles->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h6 class="mb-0">Версии песни</h6></div>
                <div class="card-body">
                    @foreach($order->audioFiles as $file)
                        <div class="mb-3">
                            <p class="mb-1 small fw-semibold">{{ $file->label ?? 'Версия ' . $loop->iteration }}</p>
                            <audio controls class="w-100">
                                <source src="{{ Storage::url($file->path) }}" type="audio/mpeg">
                                Ваш браузер не поддерживает аудио.
                            </audio>
                            @if($order->plan == 3)
                                <button form="select-form"
                                        name="selected_audio_id" value="{{ $file->id }}"
                                        class="btn btn-sm {{ $order->selected_audio_id == $file->id ? 'btn-success' : 'btn-outline-success' }} mt-1">
                                    {{ $order->selected_audio_id == $file->id ? '✓ Выбрана' : 'Выбрать эту версию' }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Cover files --}}
        @if($order->coverFiles->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h6 class="mb-0">Обложки</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($order->coverFiles as $file)
                            <div class="col-6 col-md-3">
                                <img src="{{ Storage::url($file->path) }}" class="img-fluid rounded" alt="{{ $file->label }}">
                                @if($order->plan == 3)
                                    <button form="select-form"
                                            name="selected_cover_id" value="{{ $file->id }}"
                                            class="btn btn-sm {{ $order->selected_cover_id == $file->id ? 'btn-success' : 'btn-outline-success' }} mt-1 w-100">
                                        {{ $order->selected_cover_id == $file->id ? '✓ Выбрана' : 'Выбрать' }}
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @if($order->plan == 3)
                <form id="select-form" action="{{ route('orders.select', $order) }}" method="POST" class="d-none">
                    @csrf @method('POST')
                </form>
            @endif
        @endif

        {{-- User comment --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0">Ваш комментарий к заказу</h6></div>
            <div class="card-body">
                <form action="{{ route('orders.comment', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <textarea name="user_comment" rows="4" class="form-control mb-2"
                              placeholder="Любые пожелания, уточнения...">{{ old('user_comment', $order->user_comment) }}</textarea>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Сохранить</button>
                </form>
            </div>
        </div>

        {{-- Plan upgrade --}}
        @if($order->plan < 3 && !in_array($order->status, ['pending_payment', 'canceled']))
            @php
                $nextPlan = $order->plan + 1;
                $plans = \App\Models\Order::PLANS;
                $upgradePrice = $plans[$nextPlan]['price'] - $plans[$order->plan]['price'];
            @endphp
            <div class="card shadow-sm mb-4 border-warning">
                <div class="card-header bg-warning-subtle"><h6 class="mb-0">Улучшить тариф</h6></div>
                <div class="card-body">
                    <p class="mb-3">
                        @if($order->plan == 1)
                            Хотите, чтобы мы доработали вашу песню?
                        @else
                            Хотите опубликовать песню на Яндекс Музыке и других площадках?
                        @endif
                        Перейдите на тариф
                        <strong>«{{ $plans[$nextPlan]['name'] }}»</strong>.
                    </p>
                    <p class="fs-5 fw-bold mb-3">Доплата: {{ number_format($upgradePrice, 0, '.', ' ') }} ₽</p>
                    <form action="{{ route('orders.upgrade', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            Улучшить тариф и оплатить
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Edit request --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0">Заказать правку — 400 ₽</h6></div>
            <div class="card-body">
                <form action="{{ route('orders.edit-request', $order) }}" method="POST">
                    @csrf
                    <textarea name="instructions" rows="3" class="form-control mb-2 @error('instructions') is-invalid @enderror"
                              placeholder="Опишите, что изменить: часть текста, стиль музыки, конкретный куплет...">{{ old('instructions') }}</textarea>
                    @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <button type="submit" class="btn btn-sm btn-outline-primary">Отправить заявку на правку</button>
                </form>
            </div>
        </div>

        {{-- Review --}}
        @if($order->status === 'completed' && !$order->review)
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h6 class="mb-0">Оставить отзыв</h6></div>
                <div class="card-body">
                    <form action="{{ route('orders.review', $order) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">Оценка</label>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}">
                                        <label class="form-check-label" for="rating{{ $i }}">{{ $i }}</label>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <textarea name="text" rows="3" class="form-control mb-2 @error('text') is-invalid @enderror"
                                  placeholder="Ваш отзыв..." required>{{ old('text') }}</textarea>
                        @error('text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <button type="submit" class="btn btn-sm btn-primary">Отправить отзыв</button>
                    </form>
                </div>
            </div>
        @endif

    </div>

    <div class="col-lg-4">

        {{-- Status history --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0">История статусов</h6></div>
            <div class="card-body p-0">
                @forelse($order->statusLogs as $log)
                    <div class="border-bottom px-3 py-2">
                        <div class="d-flex justify-content-between">
                            <span class="badge bg-secondary">{{ $log->statusLabel() }}</span>
                            <small class="text-muted">{{ $log->created_at->format('d.m.Y H:i') }}</small>
                        </div>
                        @if($log->comment)
                            <p class="text-muted small mb-0 mt-1">{{ $log->comment }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-3 text-muted text-center small">История пуста.</div>
                @endforelse
            </div>
        </div>

        {{-- Chat --}}
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0">Чат с менеджером</h6></div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="chat-body">
                @forelse($order->chatMessages as $msg)
                    <div class="mb-2 {{ $msg->is_admin ? 'text-start' : 'text-end' }}">
                        <div class="d-inline-block px-3 py-2 rounded-3 {{ $msg->is_admin ? 'bg-light border' : 'bg-primary text-white' }}"
                             style="max-width: 85%;">
                            <p class="mb-0 small">{{ $msg->body }}</p>
                        </div>
                        <div class="text-muted" style="font-size: 0.7rem;">
                            {{ $msg->is_admin ? 'Менеджер' : 'Вы' }} · {{ $msg->created_at->format('d.m H:i') }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center small">Сообщений пока нет.</p>
                @endforelse
            </div>
            <div class="card-footer">
                <form action="{{ route('chat.store', $order) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <textarea name="body" class="form-control" rows="2"
                                  placeholder="Написать сообщение..." required></textarea>
                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const chatBody = document.getElementById('chat-body');
    if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;
</script>
@endpush
