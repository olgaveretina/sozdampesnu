@extends('layouts.app')

@section('title', 'Наши песни — примеры работ')
@section('meta_description', 'Послушайте примеры песен, созданных нашей командой из стихов реальных клиентов. Убедитесь в качестве перед заказом.')
@section('canonical', route('songs'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">

        <div class="mb-4">
            <h1 class="fw-bold">Наши песни</h1>
            <p class="text-secondary fs-5">Послушайте примеры работ, которые мы создали для наших клиентов.</p>
        </div>

        <div class="row g-4">

            @php
            $songs = [
                ['title' => 'Провалы в сон',       'file' => 'Провалы в сон.mp3'],
                ['title' => 'Микст — это любовь',  'file' => 'Микст - это любовь.mp3'],
                ['title' => 'Худее точка ру',       'file' => 'Худее точка ру.mp3'],
                ['title' => 'Хочу в кабак',         'file' => 'Хочу в кабак.mp3'],
                ['title' => 'Девчонка у сетки',     'file' => 'Девчонка у сетки.mp3'],
                ['title' => 'Звёзды-беглянки',      'file' => 'Звёзды-беглянки.mp3'],
                ['title' => 'Она со мной',           'file' => 'Она со мной.mp3'],
            ];
            @endphp

            @foreach($songs as $i => $song)
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                 style="width:48px;height:48px;background-color:#1f2337;">
                                <i class="bi bi-music-note-beamed text-warning fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-semibold">{{ $song['title'] }}</h5>
                                <small class="text-secondary">Пример работы</small>
                            </div>
                        </div>
                        <audio controls class="w-100 mt-auto" preload="none"
                               style="accent-color:#ffc107;">
                            <source src="{{ asset('audio/' . rawurlencode($song['file'])) }}" type="audio/mpeg">
                            Ваш браузер не поддерживает аудио.
                        </audio>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <div class="card border-0 shadow-sm mt-5" style="background-color:#1f2337;">
            <div class="card-body text-center py-4">
                <h4 class="text-white fw-bold mb-2">Хотите свою песню?</h4>
                <p class="text-secondary mb-3">Пришлите ваши стихи — и мы создадим для вас уникальную песню.</p>
                <a href="{{ route('orders.create') }}" class="btn btn-warning fw-semibold px-4">
                    Заказать песню
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
