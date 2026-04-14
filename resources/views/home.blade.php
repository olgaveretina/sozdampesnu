@extends('layouts.app')

@section('full_title', 'Создать песню из стихов с помощью ИИ — Создаём песни от 600 ₽')
@section('meta_description', 'Превратите ваши стихи в профессиональную песню с помощью искусственного интеллекта. Создание песни из текста онлайн — от 700 ₽. Быстро, качественно, с гарантией возврата.')
@section('keywords', 'создать песню из стихов, сгенерировать песню с помощью искусственного интеллекта, превратить стихи в песню, стихи в песню онлайн, создание песен с помощью ИИ, заказать песню из слов, генератор песен, создать песню на основе текста, как создать песню с помощью искусственного интеллекта, песня из моих стихов')
@section('canonical', url('/'))

@push('scripts')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebSite",
  "name": "Создаём песни",
  "url": "{{ url('/') }}",
  "description": "Превращаем ваши стихи в профессиональную песню с помощью искусственного интеллекта. Создание песен онлайн от 600 ₽.",
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
      "name": "Просто попробовать",
      "price": "600",
      "priceCurrency": "RUB",
      "description": "4 варианта песни, сгенерированных ИИ"
    },
    {
      "@@type": "Offer",
      "name": "Хочу профессиональную песню",
      "price": "5000",
      "priceCurrency": "RUB",
      "description": "4 лучших варианта от ИИ с нашими доработками"
    }
  ]
}
</script>
@endpush

@section('content')

{{-- Banner placeholder --}}
<div class="text-white rounded-3 p-5 mb-5 text-center" style="min-height: 280px; display: flex; flex-direction: column; justify-content: center; background-color:#1f2337;">
    <div class="mb-3">
        <span class="badge bg-success fs-6 px-3 py-2">Мы принимаем заказы — {{ now()->translatedFormat('d F Y') }}</span>
    </div>
    <h1 class="display-5 fw-bold mb-3">Превращаем ваши стихи в песню</h1>
    <p class="lead mb-4">Отправьте нам текст — мы знаем, как сделать хорошую песню при помощи лучшего ИИ!</p>
    <p class="lead mb-4">Каждая песня требует индивидуального творческого подхода. Искусственный интеллект без человека пока не справляется =)</p>
    <div>
        <a href="{{ route('orders.create') }}" class="btn btn-warning btn-lg px-5">Заказать песню</a>
    </div>
</div>

{{-- Plans --}}
<h2 class="text-center mb-4">Стоимость</h2>
<div class="row g-4 mb-5 justify-content-center">

    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-warning">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ config('plans.1.name') }}</h5>
                <p class="text-muted small">Стихи прочитает и превратит в песню творческий человек.</p>
                <p class="text-muted small">Создать стоящую песню с помощью ИИ не так просто, как кажется. Но мы умеем!</p>
                <ul class="list-unstyled small mt-2">
                    <li>✅ 1-3 варианта</li>
                    <li>✅ Срок: от 1 часа до 2 дней</li>
                    <li>✅ Мы сгенерируем много версий, выберем лучшие, доработаем</li>
                    <li>📌 При необходимости, внесем дополнительные правки по вашим инструкциям: +400 руб.</li>
                </ul>
                <div class="mt-auto">
                    <p class="fs-4 fw-bold mb-3">{{ number_format(config('plans.1.price'), 0, '.', ' ') }} ₽</p>
                    <a href="{{ route('orders.create') }}?plan=1" class="btn btn-warning w-100">Заказать</a>
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

{{-- Refund guarantee --}}
<div class="rounded-3 p-5 mb-5 text-center" style="background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%); border: 2px solid #ffc107;">
    <div class="fs-1 mb-3">🛡️</div>
    <h3 class="fw-bold mb-2">Гарантия возврата</h3>
    <p class="text-muted mb-0" style="max-width: 620px; margin: 0 auto;">
        В редких случаях мы можем отказаться от выполнения заказа. <br />
        В такой ситуации - <strong>вернём полную стоимость</strong>.
    </p>
</div>

{{-- SEO text block --}}
<div class="bg-white rounded-3 shadow-sm p-4 mb-5">
    <h2 class="fw-bold mb-3" style="font-size:1.3rem;">Создание песен с помощью искусственного интеллекта</h2>
    <p class="text-muted">Хотите <strong>превратить стихи в песню</strong>? Наш сервис делает это быстро и профессионально. Вы присылаете любой текст — стихотворение, поздравление, признание в любви — а мы создаём готовую <strong>песню из ваших слов</strong> с помощью искусственного интеллекта и живого музыкального чутья нашей команды.</p>

    <h3 class="fw-semibold mt-4 mb-2" style="font-size:1.1rem;">Как сгенерировать песню с помощью ИИ?</h3>
    <p class="text-muted">Всё просто: вы выбираете тариф, заполняете форму заказа и оплачиваете онлайн. Наш сервис использует передовые технологии <strong>генерации песен с помощью искусственного интеллекта</strong>, чтобы создать несколько вариантов музыки под ваш текст.</p>

    <h3 class="fw-semibold mt-4 mb-2" style="font-size:1.1rem;">Стихи в песню — это реально</h3>
    <p class="text-muted">Многие думают, что превратить <strong>стихи в песню</strong> сложно. На самом деле для этого не нужны музыкальные знания — достаточно написать текст и доверить остальное нам. ИИ подберёт ритм, мелодию и настроение, а наши специалисты доработают результат до профессионального уровня. Это идеально для подарков, юбилеев, свадеб и любых особых случаев.</p>

    <h3 class="fw-semibold mt-4 mb-2" style="font-size:1.1rem;">Почему выбирают нас</h3>
    <ul class="text-muted mb-0">
        <li><strong>Доступно</strong> — создать песню из стихов можно от 700 ₽</li>
        <li><strong>Надёжно</strong> — гарантия возврата, если мы не сможем выполнить заказ</li>
        <li><strong>Просто</strong> — не нужно разбираться в музыке, просто пришлите текст</li>
    </ul>
</div>

@endsection
