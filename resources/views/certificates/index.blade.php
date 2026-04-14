@extends('layouts.app')

@section('title', 'Подарочные сертификаты на песню')
@section('meta_description', 'Подарите уникальную песню из стихов — лучший подарок на день рождения, свадьбу или юбилей. Сертификат от 1 000 ₽, действует 1 год. Моментальная доставка на email.')
@section('canonical', route('certificates.index'))

@push('styles')
<style>
    .cert-hero {
        background: linear-gradient(135deg, #1f2337 0%, #2d3154 50%, #1f2337 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 3rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .cert-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(ellipse at 30% 50%, rgba(255,193,7,0.08) 0%, transparent 50%),
                    radial-gradient(ellipse at 70% 50%, rgba(255,193,7,0.05) 0%, transparent 50%);
        pointer-events: none;
    }

    .cert-preview {
        background: linear-gradient(145deg, #1f2337 0%, #2d3154 100%);
        border: 2px solid rgba(255,193,7,0.4);
        border-radius: 1rem;
        padding: 2.5rem 2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .cert-preview::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle at top right, rgba(255,193,7,0.15) 0%, transparent 70%);
        pointer-events: none;
    }
    .cert-preview::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle at bottom left, rgba(255,193,7,0.1) 0%, transparent 70%);
        pointer-events: none;
    }
    .cert-preview .cert-logo {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        opacity: 0.9;
    }
    .cert-preview .cert-title {
        font-size: 1.5rem;
        font-weight: 300;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        color: #ffc107;
    }
    .cert-preview .cert-amount {
        font-size: 3rem;
        font-weight: 700;
        margin: 1rem 0;
    }
    .cert-preview .cert-code {
        font-family: 'Courier New', monospace;
        font-size: 1.1rem;
        letter-spacing: 0.2em;
        background: rgba(255,255,255,0.1);
        display: inline-block;
        padding: 0.4rem 1.2rem;
        border-radius: 0.5rem;
        margin-top: 0.5rem;
    }
    .cert-preview .cert-valid {
        font-size: 0.85rem;
        opacity: 0.6;
        margin-top: 1.5rem;
    }

    .amount-option {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid #dee2e6;
        border-radius: 0.75rem;
    }
    .amount-option:hover {
        border-color: #ffc107;
        box-shadow: 0 4px 15px rgba(255,193,7,0.15);
    }
    .amount-option.selected {
        border-color: #ffc107;
        background: linear-gradient(135deg, #fffdf5 0%, #fff8e1 100%);
        box-shadow: 0 4px 15px rgba(255,193,7,0.2);
    }
    .amount-option .check-icon {
        display: none;
    }
    .amount-option.selected .check-icon {
        display: inline-block;
    }

    .trust-badge {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0;
    }
    .trust-badge .icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.2rem;
    }

    .step-number {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ffc107;
        color: #1f2337;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="cert-hero mb-5">
    <div style="position:relative;z-index:1;">
        <div class="mb-3">
            <i class="bi bi-gift-fill text-warning" style="font-size: 2.5rem;"></i>
        </div>
        <h1 class="display-5 fw-bold mb-3">Подарите автору стихов его песню</h1>
        <p class="lead mb-0" style="max-width:560px;margin:0 auto;opacity:0.85;">
            Лучший подарок — тот, который невозможно купить в магазине.<br>
            Подарочный сертификат на создание песни из собственных стихов.
        </p>
    </div>
</div>

<div class="row g-5">
    {{-- Left column: certificate preview + trust --}}
    <div class="col-lg-6">

        {{-- Certificate Preview --}}
        <h5 class="text-muted text-uppercase small fw-bold mb-3" style="letter-spacing:0.1em;">Пример сертификата</h5>
        <div class="cert-preview mb-4">
            <div class="cert-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#ffc107" style="vertical-align:-2px;margin-right:4px"><path d="M9 3v10.55A4 4 0 1 0 11 17V7h4V3z"/></svg>
                Создаём песни
            </div>
            <div class="cert-title">Подарочный сертификат</div>
            <div class="text-white-50 small">на создание песни из ваших стихов</div>
            <div class="cert-amount">1 000 ₽</div>
            <div class="cert-code">GIFT-XXXX-XXXX</div>
            <div class="cert-valid">Действителен 1 год с момента покупки</div>
        </div>

        {{-- Trust signals --}}
        <div class="bg-white rounded-3 shadow-sm p-4">
            <div class="trust-badge">
                <div class="icon-wrap bg-success bg-opacity-10 text-success">
                    <i class="bi bi-lightning-fill"></i>
                </div>
                <div>
                    <div class="fw-semibold">Моментальная доставка</div>
                    <div class="text-muted small">Код сертификата на экране и на email сразу после оплаты</div>
                </div>
            </div>
            <div class="trust-badge">
                <div class="icon-wrap bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <div class="fw-semibold">Действует 1 год</div>
                    <div class="text-muted small">Получатель активирует сертификат, когда будет готов</div>
                </div>
            </div>
            <div class="trust-badge">
                <div class="icon-wrap bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-percent"></i>
                </div>
                <div>
                    <div class="fw-semibold">Покрывает 100% заказа</div>
                    <div class="text-muted small">Применяется как скидка — может оплатить весь заказ целиком</div>
                </div>
            </div>
            <div class="trust-badge">
                <div class="icon-wrap bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="fw-semibold">Безопасная оплата</div>
                    <div class="text-muted small">Платежи через ЮKassa — данные карты защищены</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right column: purchase form --}}
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">Купить сертификат</h4>

                @auth
                    <form action="{{ route('certificates.store') }}" method="POST">
                        @csrf

                        <p class="text-muted mb-3">Выберите номинал:</p>

                        <div class="d-flex flex-column gap-3 mb-4">
                            @foreach(config('plans') as $plan)
                            @if(empty($plan['disabled']))
                                <label class="amount-option p-3 d-flex align-items-center justify-content-between {{ old('amount') == $plan['price'] ? 'selected' : '' }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="radio" name="amount" value="{{ $plan['price'] }}" class="visually-hidden"
                                               {{ old('amount') == $plan['price'] ? 'checked' : '' }}>
                                        <div>
                                            <div class="fw-bold fs-5">{{ number_format($plan['price'], 0, '.', ' ') }} ₽</div>
                                            <div class="text-muted small">{{ $plan['name'] }}</div>
                                        </div>
                                    </div>
                                    <i class="bi bi-check-circle-fill text-warning fs-5 check-icon"></i>
                                </label>
                            @endif
                            @endforeach
                        </div>

                        @error('amount')<div class="alert alert-danger">{{ $message }}</div>@enderror

                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold" style="padding: 0.8rem;">
                            <i class="bi bi-gift me-2"></i>Оплатить и получить сертификат
                        </button>

                        <p class="text-muted text-center small mt-3 mb-0">
                            <i class="bi bi-lock-fill me-1"></i>Безопасная оплата через ЮKassa
                        </p>
                    </form>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-person-circle text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-4">Для покупки сертификата необходимо войти в аккаунт</p>
                        <a href="{{ route('login') }}" class="btn btn-warning btn-lg me-2 px-4">Войти</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">Зарегистрироваться</a>
                    </div>
                @endauth
            </div>
        </div>

        {{-- How it works --}}
        <div class="bg-white rounded-3 shadow-sm p-4 mt-4">
            <h5 class="fw-bold mb-4">Как это работает</h5>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="step-number">1</div>
                    <div>
                        <div class="fw-semibold">Оплачиваете сертификат</div>
                        <div class="text-muted small">Выбираете номинал и оплачиваете онлайн</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <div class="step-number">2</div>
                    <div>
                        <div class="fw-semibold">Получаете красивый сертификат</div>
                        <div class="text-muted small">С уникальным кодом — на экране и на ваш email</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <div class="step-number">3</div>
                    <div>
                        <div class="fw-semibold">Дарите близкому человеку</div>
                        <div class="text-muted small">Пересылаете картинку в мессенджере, по email или распечатываете</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <div class="step-number">4</div>
                    <div>
                        <div class="fw-semibold">Получатель создаёт свою песню</div>
                        <div class="text-muted small">Вводит код при оформлении заказа — и получает скидку</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Occasion ideas --}}
        <div class="bg-white rounded-3 shadow-sm p-4 mt-4">
            <h6 class="fw-bold mb-3">Отличный подарок на:</h6>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-light text-dark border px-3 py-2">День рождения</span>
                <span class="badge bg-light text-dark border px-3 py-2">Свадьбу</span>
                <span class="badge bg-light text-dark border px-3 py-2">Юбилей</span>
                <span class="badge bg-light text-dark border px-3 py-2">14 февраля</span>
                <span class="badge bg-light text-dark border px-3 py-2">8 марта</span>
                <span class="badge bg-light text-dark border px-3 py-2">Новый год</span>
                <span class="badge bg-light text-dark border px-3 py-2">Годовщину</span>
                <span class="badge bg-light text-dark border px-3 py-2">Просто так</span>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.amount-option').forEach(label => {
        label.addEventListener('click', () => {
            document.querySelectorAll('.amount-option').forEach(l => l.classList.remove('selected'));
            label.classList.add('selected');
            label.querySelector('input[type="radio"]').checked = true;
        });
    });
</script>
@endpush
