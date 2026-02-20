<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Создаём песни')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">🎵 Создаём песни</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('orders.create') ? 'active' : '' }}" href="{{ route('orders.create') }}">Заказать песню</a>
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

<footer class="bg-dark text-secondary py-4 mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-1">🎵 <strong class="text-white">Создаём песни</strong> — превращаем ваши стихи в музыку</p>
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
