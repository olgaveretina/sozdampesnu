<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $pageTitle = trim(view()->yieldContent('title'));
        $siteName = 'Создаём песни';
        $fullTitle = trim(view()->yieldContent('full_title')) ?: ($pageTitle ? $pageTitle . ' — ' . $siteName : $siteName);
        $metaDesc = trim(view()->yieldContent('meta_description') ?: 'Превращаем ваши стихи в профессиональную песню с помощью ИИ и нашей команды. Заказать песню онлайн от 600 ₽.');
        $canonicalUrl = trim(view()->yieldContent('canonical') ?: url()->current());
    @endphp

    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="ru_RU">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">

    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#1f2337;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#ffc107" style="vertical-align:-2px;margin-right:4px"><path d="M9 3v10.55A4 4 0 1 0 11 17V7h4V3z"/></svg>Создаём песни</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain" style="font-size:1.1rem;">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('orders.create') ? 'active' : '' }}" href="{{ route('orders.create') }}">Заказать песню</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('songs') ? 'active' : '' }}" href="{{ route('songs') }}">Наши песни</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}" href="{{ route('certificates.index') }}">Подарочные сертификаты</a>
                </li>
                @auth
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">Мои заказы</a>
                </li>
                @endauth
            </ul>
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profile*') ? 'active' : '' }}" href="{{ route('profile') }}">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-white">Выйти</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Войти</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">Регистрация</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- Mobile quick-action bar --}}
<div class="d-lg-none border-top border-secondary" style="background-color:#1f2337;">
    <div class="container">
        <div class="row g-0">
            @auth
                <div class="col-6 text-center border-end border-secondary">
                    <a href="{{ route('orders.create') }}" class="d-block py-3 text-decoration-none {{ request()->routeIs('orders.create') ? 'text-warning' : 'text-white' }}">
                        <i class="bi bi-plus-circle-fill fs-4 d-block"></i>
                        <span class="small">Новый заказ</span>
                    </a>
                </div>
                <div class="col-6 text-center">
                    <a href="{{ route('profile') }}" class="d-block py-3 text-decoration-none {{ request()->routeIs('profile*') ? 'text-warning' : 'text-white' }}">
                        <i class="bi bi-bag-heart-fill fs-4 d-block"></i>
                        <span class="small">Мои заказы</span>
                    </a>
                </div>
            @else
                <div class="col-6 text-center border-end border-secondary">
                    <a href="{{ route('orders.create') }}" class="d-block py-3 text-decoration-none {{ request()->routeIs('orders.create') ? 'text-warning' : 'text-white' }}">
                        <i class="bi bi-plus-circle-fill fs-4 d-block"></i>
                        <span class="small">Новый заказ</span>
                    </a>
                </div>
                <div class="col-6 text-center">
                    <a href="{{ route('login') }}" class="d-block py-3 text-decoration-none {{ request()->routeIs('login') ? 'text-warning' : 'text-white' }}">
                        <i class="bi bi-box-arrow-in-right fs-4 d-block"></i>
                        <span class="small">Войти</span>
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

<main class="flex-grow-1 py-4">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<footer class="text-secondary py-4 mt-auto" style="background-color:#1f2337;">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#ffc107" style="vertical-align:-2px;margin-right:4px"><path d="M9 3v10.55A4 4 0 1 0 11 17V7h4V3z"/></svg><strong class="text-white">Создаём песни</strong> — превращаем ваши стихи в музыку</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('privacy') }}" class="text-secondary text-decoration-none me-3">Политика конфиденциальности</a>
                <a href="{{ route('terms') }}" class="text-secondary text-decoration-none me-3">Пользовательское соглашение</a>
                <a href="{{ route('inn') }}" class="text-secondary text-decoration-none me-3">ИНН</a>
                <a href="{{ route('contact') }}" class="text-secondary text-decoration-none">Контакты</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
