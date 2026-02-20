@extends('layouts.app')

@section('title', 'Подарочные сертификаты')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h2 class="mb-4">Подарочные сертификаты</h2>
        <p class="lead text-muted mb-4">
            Подарите близкому человеку возможность создать свою песню. Сертификат действует 1 год. Применяется как скидка к заказу. Может оплатить 100% заказа.
        </p>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-4">Купить сертификат</h5>

                @auth
                    <form action="{{ route('certificates.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Выберите номинал</label>
                            <div class="row g-3">
                                @foreach(config('plans') as $plan)
                                    <div class="col-md-4">
                                        <label class="card h-100 text-center border-2 amount-card {{ old('amount') == $plan['price'] ? 'border-primary' : '' }}" style="cursor:pointer">
                                            <div class="card-body py-3">
                                                <input type="radio" name="amount" value="{{ $plan['price'] }}" class="visually-hidden"
                                                       {{ old('amount') == $plan['price'] ? 'checked' : '' }}>
                                                <div class="fs-5 fw-bold">{{ number_format($plan['price'], 0, '.', ' ') }} ₽</div>
                                                <div class="text-muted small">{{ $plan['name'] }}</div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="radio" name="amount" id="custom-amount-toggle" class="form-check-input" value="custom">
                                <label class="form-check-label" for="custom-amount-toggle">Другой номинал</label>
                            </div>
                            <div class="mt-2 d-none" id="custom-amount-field">
                                <input type="number" name="custom_amount" class="form-control" placeholder="Сумма в рублях" min="100">
                            </div>
                        </div>

                        @error('amount')<div class="alert alert-danger">{{ $message }}</div>@enderror

                        <button type="submit" class="btn btn-primary btn-lg w-100">Оплатить и получить сертификат</button>
                    </form>
                @else
                    <div class="text-center py-3">
                        <p class="text-muted mb-3">Для покупки сертификата необходимо войти в аккаунт.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary me-2">Войти</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">Зарегистрироваться</a>
                    </div>
                @endauth
            </div>
        </div>

        <div class="mt-4 p-3 bg-white rounded shadow-sm">
            <h6 class="mb-2">Как это работает?</h6>
            <ol class="text-muted small mb-0">
                <li>Оплачиваете сертификат на нужную сумму</li>
                <li>Получаете уникальный код на экране и на email</li>
                <li>Передаёте код получателю</li>
                <li>Получатель вводит код при оформлении заказа</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('input[name="amount"]').forEach(input => {
        input.addEventListener('change', () => {
            document.querySelectorAll('.amount-card').forEach(c => c.classList.remove('border-primary'));
            const card = input.closest('.amount-card');
            if (card) card.classList.add('border-primary');
            document.getElementById('custom-amount-field').classList.toggle('d-none', input.value !== 'custom');
        });
    });
</script>
@endpush
