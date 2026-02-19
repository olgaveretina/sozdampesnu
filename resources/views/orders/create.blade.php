@extends('layouts.app')

@section('title', 'Заказать песню')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="mb-4">Заказать песню</h2>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    {{-- Plan selection --}}
                    <h5 class="mb-3">Выберите тариф</h5>
                    <div class="row g-3 mb-4">
                        @foreach(\App\Models\Order::PLANS as $planId => $plan)
                            @if($planId == 3)
                                <div class="col-md-4">
                                    <div class="card h-100 border-2 opacity-50" style="cursor:not-allowed">
                                        <div class="card-header text-white text-center" style="background-color: #6f42c1; font-size:0.75rem; font-weight:600">Скоро в продаже</div>
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $plan['name'] }}</h6>
                                            <p class="fs-5 fw-bold mb-0">{{ number_format($plan['price'], 0, '.', ' ') }} ₽</p>
                                        </div>
                                    </div>
                                </div>
                            @else
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
                            @endif
                        @endforeach
                    </div>
                    @error('plan')<div class="alert alert-danger">{{ $message }}</div>@enderror

                    {{-- Lyrics --}}
                    <div class="mb-3">
                        <label for="lyrics" class="form-label fw-semibold">Текст песни (стихи) <span class="text-danger">*</span></label>
                        <textarea id="lyrics" name="lyrics" rows="10"
                                  class="form-control @error('lyrics') is-invalid @enderror"
                                  placeholder="Вставьте текст ваших стихов или песни..." required>{{ old('lyrics') }}</textarea>
                        @error('lyrics')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Performer --}}
                    <div class="mb-3">
                        <label for="performer_name" class="form-label fw-semibold">Имя исполнителя <span class="text-danger">*</span></label>
                        <input type="text" id="performer_name" name="performer_name"
                               class="form-control @error('performer_name') is-invalid @enderror"
                               value="{{ old('performer_name') }}"
                               placeholder="Как будет называться исполнитель?" required>
                        @error('performer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Song name --}}
                    <div class="mb-3">
                        <label for="song_name" class="form-label fw-semibold">Название песни <span class="text-danger">*</span></label>
                        <input type="text" id="song_name" name="song_name"
                               class="form-control @error('song_name') is-invalid @enderror"
                               value="{{ old('song_name') }}"
                               placeholder="Название вашей песни" required>
                        @error('song_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Music style --}}
                    <div class="mb-4">
                        <label for="music_style" class="form-label fw-semibold">Описание стиля музыки <span class="text-danger">*</span></label>
                        <textarea id="music_style" name="music_style" rows="3"
                                  class="form-control @error('music_style') is-invalid @enderror"
                                  placeholder="Например: рок-баллада в стиле 80-х, грустная мелодия с пианино, быстрый поп с гитарой..." required>{{ old('music_style') }}</textarea>
                        @error('music_style')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Plan 3 extra fields (hidden — plan 3 not available yet) --}}
                    <div id="plan3-fields" class="mb-4 d-none">
                        <div class="alert alert-info">
                            <strong>Для публикации на площадках</strong> необходима обложка. Выберите один из вариантов:
                        </div>

                        <div class="mb-3">
                            <label for="cover_description" class="form-label">Описание обложки для генерации AI</label>
                            <textarea id="cover_description" name="cover_description" rows="3"
                                      class="form-control @error('cover_description') is-invalid @enderror"
                                      placeholder="Опишите желаемую обложку...">{{ old('cover_description') }}</textarea>
                            @error('cover_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="text-center text-muted my-2">— или —</div>

                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Загрузить готовую обложку (3000×3000 px, JPG/PNG)</label>
                            <input type="file" id="cover_image" name="cover_image"
                                   class="form-control @error('cover_image') is-invalid @enderror"
                                   accept="image/jpeg,image/png">
                            @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                Я подтверждаю, что текст песни принадлежит мне или у меня есть права на его использование.
                                Я несу ответственность за правомерность использования текста.
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
    const radios = document.querySelectorAll('.plan-radio');
    const plan3Fields = document.getElementById('plan3-fields');
    const planCards = document.querySelectorAll('.plan-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('label.card').forEach(c => c.classList.remove('border-primary'));
            radio.closest('label.card').classList.add('border-primary');
            plan3Fields.classList.toggle('d-none', radio.value !== '3');
        });
    });
</script>
@endpush
