@extends('layouts.app')

@section('title', 'Заказать')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="mb-4">Оформить заказ</h2>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    {{-- Plan selection --}}
                    <h5 class="mb-3">Выберите тариф</h5>
                    <div class="row g-3 mb-4">
                        @foreach(\App\Models\Order::plans() as $planId => $plan)
                            <div class="col-md-4">
                                <label class="card h-100 border-2 {{ old('plan', request('plan', 1)) == $planId ? 'border-primary' : '' }}" style="cursor:pointer">
                                    <div class="card-body">
                                        <input type="radio" name="plan" value="{{ $planId }}" class="plan-radio visually-hidden"
                                               {{ old('plan', request('plan', 1)) == $planId ? 'checked' : '' }}>
                                        <h6 class="card-title">{{ $plan['name'] }}</h6>
                                        <p class="fs-5 fw-bold mb-0">{{ number_format($plan['price'], 0, '.', ' ') }} ₽</p>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('plan')<div class="alert alert-danger">{{ $message }}</div>@enderror

                    {{-- Common fields: performer and song name --}}
                    <div class="mb-3">
                        <label for="performer_name" class="form-label fw-semibold">Имя исполнителя <span class="text-danger">*</span></label>
                        <input type="text" id="performer_name" name="performer_name"
                               class="form-control @error('performer_name') is-invalid @enderror"
                               value="{{ old('performer_name') }}"
                               placeholder="Как будет называться исполнитель?" required>
                        @error('performer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="song_name" class="form-label fw-semibold">Название <span class="text-danger">*</span></label>
                        <input type="text" id="song_name" name="song_name"
                               class="form-control @error('song_name') is-invalid @enderror"
                               value="{{ old('song_name') }}"
                               placeholder="Название вашей песни / видеоклипа" required>
                        @error('song_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Song-only fields (plans 1 & 2) --}}
                    <div id="song-only-fields">
                        <div class="mb-3">
                            <label for="lyrics" class="form-label fw-semibold">Текст песни (стихи) <span class="text-danger">*</span></label>
                            <textarea id="lyrics" name="lyrics" rows="10"
                                      class="form-control @error('lyrics') is-invalid @enderror"
                                      placeholder="Вставьте текст ваших стихов или песни...">{{ old('lyrics') }}</textarea>
                            @error('lyrics')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="music_style" class="form-label fw-semibold">Описание стиля музыки <span class="text-danger">*</span></label>
                            <textarea id="music_style" name="music_style" rows="3"
                                      class="form-control @error('music_style') is-invalid @enderror"
                                      placeholder="Например: рок-баллада в стиле 80-х, грустная мелодия с пианино, быстрый поп с гитарой...">{{ old('music_style') }}</textarea>
                            @error('music_style')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Video-only fields (plan 3) --}}
                    <div id="video-only-fields" class="d-none">
                        <div class="alert alert-info mb-3">
                            <strong>Видеоклип:</strong> мы создадим профессиональный видеоклип на вашу песню.
                        </div>

                        <div class="mb-3">
                            <label for="video_audio" class="form-label fw-semibold">Аудио для видеоклипа <span class="text-danger">*</span></label>
                            <input type="file" id="video_audio" name="video_audio"
                                   class="form-control @error('video_audio') is-invalid @enderror"
                                   accept="audio/mpeg,audio/mp4,audio/x-m4a,audio/wav">
                            <div class="form-text">Форматы: MP3, M4A, WAV. Максимум 50 МБ.</div>
                            @error('video_audio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="singer_description" class="form-label fw-semibold">Описание исполнителя / персонажа <span class="text-danger">*</span></label>
                            <textarea id="singer_description" name="singer_description" rows="3"
                                      class="form-control @error('singer_description') is-invalid @enderror"
                                      placeholder="Опишите внешность, образ, стиль исполнителя или персонажа видеоклипа...">{{ old('singer_description') }}</textarea>
                            @error('singer_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="cover_description" class="form-label fw-semibold">Описание видеоклипа <span class="text-danger">*</span></label>
                            <textarea id="cover_description" name="cover_description" rows="4"
                                      class="form-control @error('cover_description') is-invalid @enderror"
                                      placeholder="Опишите сюжет, настроение, визуальный стиль видеоклипа...">{{ old('cover_description') }}</textarea>
                            @error('cover_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Дополнительно</p>

                        <div class="mb-4">
                            <label for="video_images" class="form-label">Фото исполнителя / сцены видеоклипа</label>
                            <input type="file" id="video_images" name="video_images[]"
                                   class="form-control @error('video_images') @error('video_images.*') is-invalid @enderror @enderror"
                                   accept="image/jpeg,image/png,image/webp" multiple>
                            <div class="form-text">До 6 фотографий (JPG, PNG, WebP, до 10 МБ каждая). Фото исполнителя, референсы сцен или настроения клипа.</div>
                            @error('video_images')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            @error('video_images.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Promo code --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="promo_code" class="form-label">Промокод</label>
                            <input type="text" id="promo_code" name="promo_code"
                                   class="form-control @error('promo_code') is-invalid @enderror"
                                   value="{{ old('promo_code') }}"
                                   placeholder="Введите промокод">
                            @error('promo_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="gift_certificate_code" class="form-label">Подарочный сертификат</label>
                            <input type="text" id="gift_certificate_code" name="gift_certificate_code"
                                   class="form-control @error('gift_certificate_code') is-invalid @enderror"
                                   value="{{ old('gift_certificate_code') }}"
                                   placeholder="Код сертификата">
                            @error('gift_certificate_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Disclaimer --}}
                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" id="disclaimer" name="disclaimer" value="1"
                                   class="form-check-input @error('disclaimer') is-invalid @enderror"
                                   {{ old('disclaimer') ? 'checked' : '' }} required>
                            <label class="form-check-label" for="disclaimer">
                                Я подтверждаю, что предоставляю материалы на законных основаниях и несу ответственность за их правомерное использование.
                            </label>
                            @error('disclaimer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">Оплатить и заказать</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const songFields  = document.getElementById('song-only-fields');
    const videoFields = document.getElementById('video-only-fields');

    function applyPlan(value) {
        const isVideo = value === '3';
        songFields.classList.toggle('d-none', isVideo);
        videoFields.classList.toggle('d-none', !isVideo);

        // Toggle required attributes
        songFields.querySelectorAll('[required], textarea, input[type=text], input[type=file]').forEach(function (el) {
            el.required = !isVideo;
        });
        videoFields.querySelectorAll('textarea, input[type=file]').forEach(function (el) {
            el.required = isVideo;
        });
    }

    document.querySelectorAll('.plan-radio').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('label.card').forEach(function (c) {
                c.classList.remove('border-primary');
            });
            radio.closest('label.card').classList.add('border-primary');
            applyPlan(radio.value);
        });
    });

    // Init on page load
    const checked = document.querySelector('.plan-radio:checked');
    if (checked) applyPlan(checked.value);
})();
</script>
@endpush
