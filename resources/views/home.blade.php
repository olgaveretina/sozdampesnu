@extends('layouts.app')

@section('title', 'Создаём песни из ваших стихов')

@section('content')

{{-- Banner placeholder --}}
<div class="bg-dark text-white rounded-3 p-5 mb-5 text-center" style="min-height: 280px; display: flex; flex-direction: column; justify-content: center;">
    <h1 class="display-5 fw-bold mb-3">Превращаем ваши стихи в песню</h1>
    <p class="lead mb-4">Отправьте нам текст — мы создадим музыку с помощью ИИ и нашей команды.</p>
    <div>
        <a href="{{ route('orders.create') }}" class="btn btn-warning btn-lg px-5">Заказать песню</a>
    </div>
</div>

{{-- Plans --}}
<h2 class="text-center mb-4">Тарифы</h2>
<div class="row g-4 mb-5">

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-warning">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ config('plans.1.name') }}</h5>
                <p class="text-muted small">4 версии песни, сгенерированные ИИ, с минимальными доработками от нас</p>
                <ul class="list-unstyled small mt-2">
                    <li>✅ 4 варианта от ИИ</li>
                    <li>✅ Срок: от 1 часа до 2 дней</li>
                    <li>📌 Мы почти не редактируем песню, вариант ИИ</li>
                    <li>📌 Правки по вашим инструкциям: +400 руб.</li>
                </ul>
                <div class="mt-auto">
                    <p class="fs-4 fw-bold mb-3">{{ number_format(config('plans.1.price'), 0, '.', ' ') }} ₽</p>
                    <a href="{{ route('orders.create') }}?plan=1" class="btn btn-warning w-100">Выбрать</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-warning">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ config('plans.2.name') }}</h5>
                <p class="text-muted small">Мы отдельно поработаем над вашей песней и пришлем 4 удачные версии</p>
                <ul class="list-unstyled small mt-2">
                    <li>✅ 4 лучших варианта от ИИ, с нашими доработками</li>
                    <li>✅ Срок: от 2 часов до 2 дней</li>
                    <li>📌 Правки по вашим инструкциям: +400 руб.</li>
                </ul>
                <div class="mt-auto">
                    <p class="fs-4 fw-bold mb-3">{{ number_format(config('plans.2.price'), 0, '.', ' ') }} ₽</p>
                    <a href="{{ route('orders.create') }}?plan=2" class="btn btn-warning w-100">Выбрать</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-warning">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ config('plans.3.name') }}</h5>
                <p class="text-muted small">Это кропотливая работа, но мы справимся! =) Вы можете описать, как будет выглядеть клип.</p>
                <ul class="list-unstyled small mt-2">
                    <li>✅ Предоставим варианты исполнителей, выбор за вами</li>
                    <li>✅ Срок - от 2 до 14 дней</li>
                    <li>✅ Клип будет выглядеть профессионально</li>
                    <li>📌 Вы предоставляете полностью готовый аудиофайл</li>
                    <li>📌 Правки по вашим инструкциям: от 400 руб.</li>
                </ul>
                <div class="mt-auto">
                    <p class="fs-4 fw-bold mb-3">{{ number_format(config('plans.3.price'), 0, '.', ' ') }} ₽</p>
                    <a href="{{ route('orders.create') }}?plan=3" class="btn btn-warning w-100">Выбрать</a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- How it works --}}
<div class="bg-white rounded-3 shadow-sm p-4 mb-5">
    <h2 class="text-center mb-4">Как это работает</h2>
    <div class="row text-center g-4">
        <div class="col-md-3">
            <div class="fs-1 mb-2">✍️</div>
            <h6>Пишете текст</h6>
            <p class="text-muted small">Отправляете нам свои стихи или текст будущей песни</p>
        </div>
        <div class="col-md-3">
            <div class="fs-1 mb-2">💳</div>
            <h6>Оплачиваете</h6>
            <p class="text-muted small">Выбираете тариф и оплачиваете заказ онлайн</p>
        </div>
        <div class="col-md-3">
            <div class="fs-1 mb-2">🎵</div>
            <h6>Создаём песню</h6>
            <p class="text-muted small">Наша команда генерирует и дорабатывает варианты</p>
        </div>
        <div class="col-md-3">
            <div class="fs-1 mb-2">📥</div>
            <h6>Получаете</h6>
            <p class="text-muted small">Готовые файлы появляются в вашем личном кабинете</p>
        </div>
    </div>
</div>

@endsection
