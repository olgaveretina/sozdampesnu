@extends('layouts.app')

@section('title', 'Личный кабинет')

@section('content')
<div class="row g-4">

    {{-- Orders --}}
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Мои заказы</h5>
                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary">+ Новый заказ</a>
            </div>
            <div class="card-body p-0">
                @forelse($orders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="d-block text-decoration-none text-dark border-bottom px-4 py-3 hover-bg">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $order->song_name ?? $order->performer_name }}</strong>
                                <span class="text-muted ms-2 small">{{ $order->performer_name }} · {{ $order->planLabel() }}</span>
                                <span class="badge bg-info text-dark ms-1">{{ $order->typeLabel() }}</span>
                            </div>
                            <span class="badge bg-secondary">{{ $order->statusLabel() }}</span>
                        </div>
                        <div class="text-muted small mt-1">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                    </a>
                @empty
                    <div class="p-4 text-muted text-center">У вас пока нет заказов.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Settings --}}
    <div class="col-lg-4">

        {{-- Account info --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0">Аккаунт</h6></div>
            <div class="card-body">
                <p class="mb-1 small text-muted">Email</p>
                <p class="mb-0 fw-semibold">{{ auth()->user()->email }}</p>
            </div>
        </div>

        {{-- Change name --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0">Изменить имя</h6></div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Сохранить</button>
                </form>
            </div>
        </div>

        {{-- Change email --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0">Изменить email</h6></div>
            <div class="card-body">
                <form action="{{ route('profile.email') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Сохранить</button>
                </form>
            </div>
        </div>

        {{-- Telegram --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.287 5.906c-.778.324-2.334.994-4.666 2.01-.378.15-.577.298-.595.442-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294.26.006.549-.1.868-.32 2.179-1.471 3.304-2.214 3.374-2.23.05-.012.12-.026.166.016.047.041.042.12.037.141-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8.154 8.154 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629.093.06.183.125.27.187.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.426 1.426 0 0 0-.013-.315.337.337 0 0 0-.114-.217.526.526 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09z"/>
                </svg>
                <h6 class="mb-0">Telegram уведомления</h6>
            </div>
            <div class="card-body">
                @if(auth()->user()->telegram_chat_id)
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-success">Привязан</span>
                    </div>
                    <p class="small text-muted mb-3">Вы получаете уведомления об изменении статуса заказов.</p>
                    <form action="{{ route('profile.telegram.unlink') }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Отвязать Telegram</button>
                    </form>
                @elseif(auth()->user()->telegram_bind_token)
                    <p class="small text-muted mb-3">Ожидание подтверждения в Telegram...</p>
                    @php $botUsername = config('services.telegram.bot_username'); $token = auth()->user()->telegram_bind_token; @endphp
                    <a href="https://t.me/{{ $botUsername }}?start={{ $token }}"
                       class="btn btn-sm btn-primary w-100 mb-2" target="_blank">
                        Открыть Telegram снова
                    </a>
                    <form action="{{ route('profile.telegram.bind') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link w-100 text-muted">Получить новую ссылку</button>
                    </form>
                @else
                    <p class="small text-muted mb-3">Привяжите Telegram для получения уведомлений о статусе заказов.</p>
                    <form action="{{ route('profile.telegram.bind') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                            Привязать Telegram
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Change password --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0">Изменить пароль</h6></div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-2">
                        <input type="password" name="current_password" placeholder="Текущий пароль"
                               class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <input type="password" name="password" placeholder="Новый пароль"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password_confirmation" placeholder="Повторите пароль"
                               class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">Изменить пароль</button>
                </form>
            </div>
        </div>

        {{-- Delete account --}}
        <div class="card shadow-sm border-danger">
            <div class="card-header text-danger"><h6 class="mb-0">Удалить аккаунт</h6></div>
            <div class="card-body">
                <form action="{{ route('profile.destroy') }}" method="POST"
                      onsubmit="return confirm('Вы уверены? Это действие необратимо.')">
                    @csrf @method('DELETE')
                    <div class="mb-2">
                        <input type="password" name="password" placeholder="Введите пароль для подтверждения"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">Удалить аккаунт</button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
