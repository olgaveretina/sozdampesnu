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
