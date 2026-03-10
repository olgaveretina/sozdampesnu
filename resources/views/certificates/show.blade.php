@extends('layouts.app')

@section('title', 'Ваш подарочный сертификат')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 text-center">

        <div class="mb-3">
            <span class="fs-1">🎉</span>
        </div>
        <h2 class="fw-bold mb-1">Сертификат готов!</h2>
        <p class="text-muted mb-4">Скачайте изображение и подарите его получателю</p>

        {{-- Certificate card (captured for download) --}}
        <div id="cert-card" style="
            background: linear-gradient(135deg, #1f2337 0%, #2d3561 60%, #1f2337 100%);
            border-radius: 20px;
            padding: 48px 40px;
            position: relative;
            overflow: hidden;
            text-align: center;
            color: #fff;
            box-shadow: 0 8px 40px rgba(0,0,0,0.18);
            max-width: 600px;
            margin: 0 auto 28px;
        ">
            {{-- Decorative circles --}}
            <div style="position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,193,7,0.10);"></div>
            <div style="position:absolute;bottom:-80px;left:-60px;width:240px;height:240px;border-radius:50%;background:rgba(255,193,7,0.07);"></div>

            {{-- Top label --}}
            <div style="font-size:13px;letter-spacing:3px;text-transform:uppercase;color:#ffc107;margin-bottom:18px;font-weight:600;">
                Подарочный сертификат
            </div>

            {{-- Site name --}}
            <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffc107" style="width:30px;height:30px;flex-shrink:0;">
                    <path d="M9 3v10.55A4 4 0 1 0 11 17V7h4V3z"/>
                </svg>
                <span style="font-size:24px;font-weight:700;color:#fff;">ИИ песни</span>
            </div>
            <div style="font-size:15px;color:rgba(255,255,255,0.6);margin-bottom:28px;">
                Превращаем стихи в песню
            </div>

            {{-- Amount --}}
            <div style="
                display:inline-block;
                background:rgba(255,193,7,0.15);
                border:2px solid #ffc107;
                border-radius:12px;
                padding:14px 36px;
                margin-bottom:28px;
            ">
                <div style="font-size:13px;color:#ffc107;margin-bottom:4px;letter-spacing:1px;">Номинал</div>
                <div style="font-size:42px;font-weight:800;color:#ffc107;line-height:1;">
                    {{ number_format($cert->amount_rub, 0, '.', ' ') }} ₽
                </div>
            </div>

            {{-- Code --}}
            <div style="margin-bottom:28px;">
                <div style="font-size:12px;color:rgba(255,255,255,0.55);margin-bottom:8px;letter-spacing:2px;text-transform:uppercase;">Код сертификата</div>
                <div style="
                    font-family: 'Courier New', monospace;
                    font-size:28px;
                    font-weight:700;
                    letter-spacing:6px;
                    background:rgba(255,255,255,0.08);
                    border:1.5px dashed rgba(255,255,255,0.25);
                    border-radius:10px;
                    padding:12px 20px;
                    display:inline-block;
                ">{{ $cert->code }}</div>
            </div>

            {{-- Footer note --}}
            <div style="font-size:15px;color:rgba(255,255,255,0.45);line-height:1.6;">
                Введите код при оформлении заказа на aipesni.ru<br>
                Действителен 1 год · Можно оплатить до 100% заказа
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <button id="btn-download" class="btn btn-warning btn-lg px-5">
                Скачать изображение
            </button>
            <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                Купить ещё
            </a>
        </div>

        <p class="text-muted small mt-4">
            Передайте код получателю — он введёт его при оформлении заказа на нашем сайте.
        </p>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
document.getElementById('btn-download').addEventListener('click', function () {
    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Подготовка...';

    html2canvas(document.getElementById('cert-card'), {
        scale: 2,
        backgroundColor: null,
        useCORS: true,
    }).then(function (canvas) {
        const link = document.createElement('a');
        link.download = 'сертификат-{{ $cert->code }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        btn.disabled = false;
        btn.textContent = 'Скачать изображение';
    });
});
</script>
@endpush
