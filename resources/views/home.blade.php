@extends('layouts.app')

@section('full_title', 'Создать песню из ваших стихов — профессионально, от 1 000 ₽ | Создаём песни')
@section('meta_description', 'Пришлите стихи — получите готовую песню. Профессиональная команда + ИИ-технологии. Результат от 1 часа, от 1 000 ₽. Гарантия возврата. Уже сделали сотни песен для наших клиентов.')
@section('keywords', 'создать песню из стихов, превратить стихи в песню, заказать песню онлайн, песня из моих стихов, стихи в песню с помощью ИИ, создание песен из текста, генерация песни ИИ, песня на заказ, подарить песню, песня в подарок')
@section('canonical', url('/'))

@push('scripts')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebSite",
  "name": "Создаём песни",
  "url": "{{ url('/') }}",
  "description": "Превращаем ваши стихи в профессиональную песню с помощью искусственного интеллекта. Создание песен онлайн от 1 000 ₽.",
  "potentialAction": {
    "@@type": "OrderAction",
    "target": "{{ route('orders.create') }}"
  }
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Service",
  "name": "Создание песен из стихов с помощью ИИ",
  "url": "{{ url('/') }}",
  "description": "Сервис по созданию профессиональных песен на основе ваших стихов и текстов с применением технологий искусственного интеллекта.",
  "provider": {
    "@@type": "Organization",
    "name": "Создаём песни",
    "url": "{{ url('/') }}"
  },
  "areaServed": "RU",
  "inLanguage": "ru",
  "offers": [
    {
      "@@type": "Offer",
      "name": "{{ config('plans.1.name') }}",
      "price": "{{ config('plans.1.price') }}",
      "priceCurrency": "RUB",
      "description": "Профессиональная песня из ваших стихов"
    }
  ]
}
</script>
@endpush

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #1f2337 0%, #2d3154 50%, #1f2337 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 4rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(ellipse at 20% 50%, rgba(255,193,7,0.08) 0%, transparent 50%),
                    radial-gradient(ellipse at 80% 50%, rgba(255,193,7,0.05) 0%, transparent 50%);
        pointer-events: none;
    }
    .hero-section > * { position: relative; z-index: 1; }

    .feature-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .step-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #ffc107;
        color: #1f2337;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .step-connector {
        position: absolute;
        left: 23px;
        top: 56px;
        bottom: -8px;
        width: 2px;
        background: #ffc107;
        opacity: 0.3;
    }

    .plan-card {
        border: 2px solid #ffc107;
        border-radius: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .plan-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(255,193,7,0.15);
    }

    .sample-card {
        transition: transform 0.2s;
    }
    .sample-card:hover {
        transform: translateY(-2px);
    }

    .gift-banner {
        background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%);
        border: 2px solid #ffc107;
        border-radius: 1rem;
    }

    .guarantee-section {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 2px solid #22c55e;
        border-radius: 1rem;
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="hero-section mb-5">
    <div class="mb-3">
        <span class="badge bg-success fs-6 px-3 py-2">Мы принимаем заказы — {{ now()->translatedFormat('d F Y') }}</span>
    </div>
    <h1 class="display-4 fw-bold mb-3">Превращаем ваши стихи<br>в настоящую песню</h1>
    <p class="lead mb-2" style="max-width:620px;margin:0 auto;opacity:0.85;">
        Отправьте нам текст — мы создадим профессиональную песню с помощью лучших технологий ИИ и живого музыкального чутья нашей команды.
    </p>
    <p class="mb-4" style="opacity:0.6;">Результат от 1 часа. Каждая песня — индивидуальный творческий подход.</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="{{ route('orders.create') }}" class="btn btn-warning btn-lg px-5 fw-bold">Заказать песню</a>
        <a href="{{ route('songs') }}" class="btn btn-outline-light btn-lg px-4">Послушать примеры</a>
    </div>
</div>

{{-- Trust bar --}}
<div class="row g-3 mb-5">
    <div class="col-md-4">
        <div class="bg-white rounded-3 shadow-sm p-3 h-100 d-flex align-items-center gap-3">
            <div class="feature-icon bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-lightning-fill"></i>
            </div>
            <div>
                <div class="fw-bold">От 1 часа</div>
                <div class="text-muted small">Быстрый результат без потери качества</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bg-white rounded-3 shadow-sm p-3 h-100 d-flex align-items-center gap-3">
            <div class="feature-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="fw-bold">Гарантия возврата</div>
                <div class="text-muted small">Вернём деньги, если не сможем выполнить</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bg-white rounded-3 shadow-sm p-3 h-100 d-flex align-items-center gap-3">
            <div class="feature-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="fw-bold">ИИ + живая команда</div>
                <div class="text-muted small">Генерируем, слушаем, дорабатываем</div>
            </div>
        </div>
    </div>
</div>

{{-- Audio samples --}}
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Послушайте наши работы</h2>
        <a href="{{ route('songs') }}" class="text-decoration-none fw-semibold">Все песни <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-4">
        @php
        $featured = [
            ['title' => 'Она со мной',        'file' => 'Она со мной.mp3',        'artist' => 'Виктория'],
            ['title' => 'Провалы в сон',      'file' => 'Провалы в сон.mp3',      'artist' => 'Астрал'],
            ['title' => 'Худее точка ру',     'file' => 'Худее точка ру.mp3',     'artist' => 'Настюха'],
        ];
        @endphp
        @foreach($featured as $song)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 sample-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                             style="width:44px;height:44px;background-color:#1f2337;">
                            <i class="bi bi-music-note-beamed text-warning"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">{{ $song['title'] }}</h6>
                            <small class="text-muted">{{ $song['artist'] }}</small>
                        </div>
                    </div>
                    <audio controls class="w-100" preload="metadata" style="accent-color:#ffc107;">
                        <source src="{{ asset('audio/' . rawurlencode($song['file'])) }}" type="audio/mpeg">
                    </audio>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Plan --}}
<div class="mb-5">
    <h2 class="text-center fw-bold mb-4">Стоимость</h2>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card plan-card shadow-sm border-0">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="text-center mb-3">
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6">Популярный выбор</span>
                    </div>
                    <h3 class="text-center fw-bold mb-3">{{ config('plans.1.name') }}</h3>
                    <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span>1-3 варианта песни на выбор</span>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span>Срок: от 1 часа до 2 дней</span>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span>Генерируем десятки версий, выбираем лучшие, дорабатываем</span>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span>Файлы песни в вашем личном кабинете</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle text-muted mt-1"></i>
                            <span class="text-muted">Дополнительные правки по вашим инструкциям: +400 ₽</span>
                        </li>
                    </ul>
                    <div class="text-center mt-auto">
                        <div class="fs-2 fw-bold mb-3">{{ number_format(config('plans.1.price'), 0, '.', ' ') }} ₽</div>
                        <a href="{{ route('orders.create') }}?plan=1" class="btn btn-warning btn-lg w-100 fw-bold" style="padding: 0.8rem;">Заказать песню</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- How it works --}}
<div class="bg-white rounded-3 shadow-sm p-4 p-lg-5 mb-5">
    <h2 class="text-center fw-bold mb-5">Как это работает</h2>
    <div class="row g-4">
        <div class="col-md-3 text-center">
            <div class="step-circle mx-auto mb-3">1</div>
            <h6 class="fw-bold">Пишете текст</h6>
            <p class="text-muted small mb-0">Отправляете свои стихи или текст будущей песни через форму заказа</p>
        </div>
        <div class="col-md-3 text-center">
            <div class="step-circle mx-auto mb-3">2</div>
            <h6 class="fw-bold">Оплачиваете</h6>
            <p class="text-muted small mb-0">Безопасная оплата онлайн через ЮKassa — данные карты защищены</p>
        </div>
        <div class="col-md-3 text-center">
            <div class="step-circle mx-auto mb-3">3</div>
            <h6 class="fw-bold">Создаём песню</h6>
            <p class="text-muted small mb-0">Генерируем множество вариантов, слушаем, отбираем и дорабатываем лучшие</p>
        </div>
        <div class="col-md-3 text-center">
            <div class="step-circle mx-auto mb-3">4</div>
            <h6 class="fw-bold">Получаете результат</h6>
            <p class="text-muted small mb-0">Готовые файлы появляются в личном кабинете, вы можете скачать и слушать</p>
        </div>
    </div>
</div>

{{-- Gift certificate banner --}}
<div class="gift-banner p-4 p-lg-5 mb-5">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-2">
                <i class="bi bi-gift-fill text-warning fs-2"></i>
                <h3 class="fw-bold mb-0">Подарите песню близкому человеку</h3>
            </div>
            <p class="text-muted mb-lg-0">
                Подарочный сертификат на создание песни из стихов — уникальный подарок на день рождения, свадьбу или юбилей. Моментальная доставка на email.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('certificates.index') }}" class="btn btn-warning btn-lg fw-bold px-4">Купить сертификат</a>
        </div>
    </div>
</div>

{{-- Guarantee --}}
<div class="guarantee-section p-4 p-lg-5 mb-5 text-center">
    <i class="bi bi-shield-check text-success" style="font-size: 2.5rem;"></i>
    <h3 class="fw-bold mt-3 mb-2">Гарантия возврата</h3>
    <p class="text-muted mb-0" style="max-width: 620px; margin: 0 auto;">
        В редких случаях мы можем отказаться от выполнения заказа.<br>
        В такой ситуации — <strong>вернём полную стоимость</strong>.
    </p>
</div>

{{-- SEO text --}}
<div class="bg-white rounded-3 shadow-sm p-4 mb-5">
    <h2 class="fw-bold mb-3" style="font-size:1.3rem;">Создание песен с помощью искусственного интеллекта</h2>
    <p class="text-muted">Хотите <strong>превратить стихи в песню</strong>? Наш сервис делает это быстро и профессионально. Вы присылаете любой текст — стихотворение, поздравление, признание в любви — а мы создаём готовую <strong>песню из ваших слов</strong> с помощью искусственного интеллекта и живого музыкального чутья нашей команды.</p>

    <h3 class="fw-semibold mt-4 mb-2" style="font-size:1.1rem;">Как сгенерировать песню с помощью ИИ?</h3>
    <p class="text-muted">Всё просто: вы выбираете тариф, заполняете форму заказа и оплачиваете онлайн. Наш сервис использует передовые технологии <strong>генерации песен с помощью искусственного интеллекта</strong>, чтобы создать несколько вариантов музыки под ваш текст.</p>

    <h3 class="fw-semibold mt-4 mb-2" style="font-size:1.1rem;">Стихи в песню — это реально</h3>
    <p class="text-muted">Многие думают, что превратить <strong>стихи в песню</strong> сложно. На самом деле для этого не нужны музыкальные знания — достаточно написать текст и доверить остальное нам. ИИ подберёт ритм, мелодию и настроение, а наши специалисты доработают результат до профессионального уровня. Это идеально для подарков, юбилеев, свадеб и любых особых случаев.</p>

    <h3 class="fw-semibold mt-4 mb-2" style="font-size:1.1rem;">Почему выбирают нас</h3>
    <ul class="text-muted mb-0">
        <li><strong>Доступно</strong> — создать песню из стихов можно от {{ number_format(config('plans.1.price'), 0, '.', ' ') }} ₽</li>
        <li><strong>Надёжно</strong> — гарантия возврата, если мы не сможем выполнить заказ</li>
        <li><strong>Просто</strong> — не нужно разбираться в музыке, просто пришлите текст</li>
    </ul>
</div>

@endsection
