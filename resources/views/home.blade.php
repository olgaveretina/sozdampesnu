@extends('layouts.app')

@section('title', 'Создаём песни из ваших стихов')

@section('content')

{{-- Banner placeholder --}}
<div class="bg-dark text-white rounded-3 p-5 mb-5 text-center" style="min-height: 280px; display: flex; flex-direction: column; justify-content: center;">
    <h1 class="display-5 fw-bold mb-3">Превращаем ваши стихи в песню</h1>
    <p class="lead mb-4">Отправьте нам текст — мы создадим музыку с помощью AI и нашей команды.</p>
    <div>
        <a href="{{ route('orders.create') }}" class="btn btn-warning btn-lg px-5">Заказать песню</a>
    </div>
</div>

{{-- Plans --}}
<h2 class="text-center mb-4">Тарифы</h2>
<div class="row g-4 mb-5">

    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Просто попробовать</h5>
                <p class="text-muted small">4 версии песни, сгенерированные Suno AI без нашей доработки</p>
                <ul class="list-unstyled small mt-2">
                    <li>✅ 4 варианта от AI</li>
                    <li>✅ Срок: от 1 часа до 2 дней</li>
                    <li>📌 Позже можно улучшить</li>
                </ul>
                <div class="mt-auto">
                    <p class="fs-4 fw-bold mb-3">600 ₽</p>
                    <a href="{{ route('orders.create') }}?plan=1" class="btn btn-outline-primary w-100">Выбрать</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-primary">
            <div class="card-header bg-primary text-white text-center small fw-bold">Популярный выбор</div>
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Хочу крутую песню</h5>
                <p class="text-muted small">Мы отдельно поработаем над вашей песней и передадим 4 доработанные версии</p>
                <ul class="list-unstyled small mt-2">
                    <li>✅ 4 варианта с нашей доработкой</li>
                    <li>✅ Срок: от 2 часов до 2 дней</li>
                    <li>📌 Позже можно опубликовать</li>
                </ul>
                <div class="mt-auto">
                    <p class="fs-4 fw-bold mb-3">5 000 ₽</p>
                    <a href="{{ route('orders.create') }}?plan=2" class="btn btn-primary w-100">Выбрать</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Крутая песня + публикация</h5>
                <p class="text-muted small">Доработаем песню и опубликуем на Яндекс Музыке и других площадках</p>
                <ul class="list-unstyled small mt-2">
                    <li>✅ 4 варианта с доработкой</li>
                    <li>✅ Обложка для площадок</li>
                    <li>✅ Публикация ~10 дней после выбора</li>
                    <li>✅ Доход от стриминга — ваш</li>
                </ul>
                <div class="mt-auto">
                    <p class="fs-4 fw-bold mb-3">15 000 ₽</p>
                    <a href="{{ route('orders.create') }}?plan=3" class="btn btn-outline-primary w-100">Выбрать</a>
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
