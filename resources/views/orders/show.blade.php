@extends('layouts.app')

@section('title', 'Заказ — ' . $order->performer_name)

@section('content')
<div class="row g-4">
    <div class="col-lg-8">

        {{-- Order header --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-0">{{ $order->performer_name }}</h4>
                        @if($order->song_name)
                            <p class="mb-1 text-secondary">«{{ $order->song_name }}»</p>
                        @endif
                        <span class="text-muted small">{{ $order->planLabel() }} · {{ $order->created_at->format('d.m.Y') }}</span>
                    </div>
                    <span class="badge bg-primary fs-6">{{ $order->statusLabel() }}</span>
                </div>
            </div>
        </div>

        {{-- Order details --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <a class="d-flex justify-content-between align-items-center text-decoration-none text-dark"
                   data-bs-toggle="collapse" href="#order-details" role="button" aria-expanded="false">
                    <h6 class="mb-0">Детали заказа</h6>
                    <span class="text-muted small">показать ▾</span>
                </a>
            </div>
            <div class="collapse" id="order-details">
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Имя исполнителя</p>
                        <p class="mb-0">{{ $order->performer_name }}</p>
                    </div>
                    @if($order->song_name)
                        <div class="mb-3">
                            <p class="text-muted small mb-1">Название песни</p>
                            <p class="mb-0">{{ $order->song_name }}</p>
                        </div>
                    @endif
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Стиль музыки</p>
                        <p class="mb-0">{{ $order->music_style }}</p>
                    </div>
                    <div class="mb-0">
                        <p class="text-muted small mb-1">Текст песни (стихи)</p>
                        <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit; font-size: 0.95rem;">{{ $order->lyrics }}</pre>
                    </div>
                    @if($order->cover_description)
                        <div class="mt-3">
                            <p class="text-muted small mb-1">Описание обложки</p>
                            <p class="mb-0">{{ $order->cover_description }}</p>
                        </div>
                    @endif
                    @if($order->cover_image_path)
                        <div class="mt-3">
                            <p class="text-muted small mb-1">Загруженная обложка</p>
                            <img src="{{ Storage::url($order->cover_image_path) }}" class="img-fluid rounded" style="max-height: 200px;" alt="Обложка">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Audio files --}}
        <div id="audio-section" class="card shadow-sm mb-4"@if($order->audioFiles->isEmpty()) style="display:none"@endif>
            <div class="card-header"><h6 class="mb-0">Версии песни</h6></div>
            <div class="card-body" id="audio-files-body">
                @foreach($order->audioFiles as $file)
                    <div class="mb-3" data-file-id="{{ $file->id }}" data-file-type="audio">
                        <p class="mb-1 small fw-semibold">{{ $file->label ?? 'Версия ' . $loop->iteration }}</p>
                        <audio controls class="w-100">
                            <source src="{{ Storage::url($file->path) }}" type="audio/mpeg">
                            Ваш браузер не поддерживает аудио.
                        </audio>
                        @if($order->plan == 3)
                            <button form="select-form"
                                    name="selected_audio_id" value="{{ $file->id }}"
                                    class="btn btn-sm {{ $order->selected_audio_id == $file->id ? 'btn-success' : 'btn-outline-success' }} mt-1">
                                {{ $order->selected_audio_id == $file->id ? '✓ Выбрана' : 'Выбрать эту версию' }}
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Cover files --}}
        <div id="cover-section" class="card shadow-sm mb-4"@if($order->coverFiles->isEmpty()) style="display:none"@endif>
            <div class="card-header"><h6 class="mb-0">Обложки</h6></div>
            <div class="card-body">
                <div class="row g-3" id="cover-files-row">
                    @foreach($order->coverFiles as $file)
                        <div class="col-6 col-md-3" data-file-id="{{ $file->id }}" data-file-type="cover">
                            <img src="{{ Storage::url($file->path) }}" class="img-fluid rounded" alt="{{ $file->label }}">
                            @if($order->plan == 3)
                                <button form="select-form"
                                        name="selected_cover_id" value="{{ $file->id }}"
                                        class="btn btn-sm {{ $order->selected_cover_id == $file->id ? 'btn-success' : 'btn-outline-success' }} mt-1 w-100">
                                    {{ $order->selected_cover_id == $file->id ? '✓ Выбрана' : 'Выбрать' }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($order->plan == 3)
            <form id="select-form" action="{{ route('orders.select', $order) }}" method="POST" class="d-none">
                @csrf @method('POST')
            </form>
        @endif

        {{-- User comment --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0">Ваш комментарий к заказу</h6></div>
            <div class="card-body">
                <form action="{{ route('orders.comment', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <textarea name="user_comment" rows="4" class="form-control mb-2"
                              placeholder="Любые пожелания, уточнения...">{{ old('user_comment', $order->user_comment) }}</textarea>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Сохранить</button>
                </form>
            </div>
        </div>

        {{-- Edit request --}}
        @if($order->audioFiles->isNotEmpty())
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0">Заказать правку — 400 ₽</h6></div>
            <div class="card-body">
                <form action="{{ route('orders.edit-request', $order) }}" method="POST">
                    @csrf
                    <textarea name="instructions" rows="3" class="form-control mb-2 @error('instructions') is-invalid @enderror"
                              placeholder="Опишите, что изменить: часть текста, стиль музыки, конкретный куплет...">{{ old('instructions') }}</textarea>
                    @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <button type="submit" class="btn btn-sm btn-outline-primary">Отправить заявку на правку</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Review --}}
        @if($order->status === 'completed' && !$order->review)
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h6 class="mb-0">Оставить отзыв</h6></div>
                <div class="card-body">
                    <form action="{{ route('orders.review', $order) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">Оценка</label>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}">
                                        <label class="form-check-label" for="rating{{ $i }}">{{ $i }}</label>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <textarea name="text" rows="3" class="form-control mb-2 @error('text') is-invalid @enderror"
                                  placeholder="Ваш отзыв..." required>{{ old('text') }}</textarea>
                        @error('text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <button type="submit" class="btn btn-sm btn-primary">Отправить отзыв</button>
                    </form>
                </div>
            </div>
        @endif

    </div>

    <div class="col-lg-4">

        {{-- Status history --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0">История статусов</h6></div>
            <div class="card-body p-0">
                @forelse($order->statusLogs as $log)
                    <div class="border-bottom px-3 py-2">
                        <div class="d-flex justify-content-between">
                            <span class="badge bg-secondary">{{ $log->statusLabel() }}</span>
                            <small class="text-muted">{{ $log->created_at->format('d.m.Y H:i') }}</small>
                        </div>
                        @if($log->comment)
                            <p class="text-muted small mb-0 mt-1">{{ $log->comment }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-3 text-muted text-center small">История пуста.</div>
                @endforelse
            </div>
        </div>

        {{-- Chat --}}
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0">Чат с менеджером</h6></div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="chat-body">
                @forelse($order->chatMessages as $msg)
                    <div class="mb-2 {{ $msg->is_admin ? 'text-start' : 'text-end' }}" data-msg-id="{{ $msg->id }}">
                        <div class="d-inline-block px-3 py-2 rounded-3 {{ $msg->is_admin ? 'bg-light border' : 'bg-primary text-white' }}"
                             style="max-width: 85%;">
                            <p class="mb-0 small">{{ $msg->body }}</p>
                        </div>
                        <div class="text-muted" style="font-size: 0.7rem;">
                            {{ $msg->is_admin ? 'Менеджер' : 'Вы' }} · {{ $msg->created_at->format('d.m H:i') }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center small">Сообщений пока нет.</p>
                @endforelse
            </div>
            <div class="card-footer">
                <form action="{{ route('chat.store', $order) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <textarea name="body" class="form-control" rows="2"
                                  placeholder="Написать сообщение..." required></textarea>
                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
// ── File polling ─────────────────────────────────────────────────────────────
(function () {
    const pollUrl  = '{{ route('orders.files', $order) }}';
    const plan     = {{ $order->plan }};
    let lastId     = 0;
    let audioCount = 0;

    document.querySelectorAll('[data-file-id]').forEach(function (el) {
        const id = parseInt(el.dataset.fileId, 10);
        if (id > lastId) lastId = id;
        if (el.dataset.fileType === 'audio') audioCount++;
    });

    function esc(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function renderAudio(file) {
        audioCount++;
        const div = document.createElement('div');
        div.className = 'mb-3';
        div.dataset.fileId   = file.id;
        div.dataset.fileType = 'audio';
        let html = '<p class="mb-1 small fw-semibold">' + esc(file.label || 'Версия ' + audioCount) + '</p>';
        html += '<audio controls class="w-100"><source src="' + esc(file.url) + '" type="audio/mpeg">Ваш браузер не поддерживает аудио.</audio>';
        if (plan === 3) {
            html += '<button form="select-form" name="selected_audio_id" value="' + file.id + '" class="btn btn-sm btn-outline-success mt-1">Выбрать эту версию</button>';
        }
        div.innerHTML = html;
        return div;
    }

    function renderCover(file) {
        const div = document.createElement('div');
        div.className = 'col-6 col-md-3';
        div.dataset.fileId   = file.id;
        div.dataset.fileType = 'cover';
        let html = '<img src="' + esc(file.url) + '" class="img-fluid rounded" alt="' + esc(file.label) + '">';
        if (plan === 3) {
            html += '<button form="select-form" name="selected_cover_id" value="' + file.id + '" class="btn btn-sm btn-outline-success mt-1 w-100">Выбрать</button>';
        }
        div.innerHTML = html;
        return div;
    }

    function poll() {
        fetch(pollUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (files) {
                const audioBody = document.getElementById('audio-files-body');
                const coverRow  = document.getElementById('cover-files-row');

                const serverAudioIds = new Set();
                const serverCoverIds = new Set();
                files.forEach(function (f) {
                    if (f.type === 'audio') serverAudioIds.add(f.id);
                    else if (f.type === 'cover') serverCoverIds.add(f.id);
                });

                // Remove deleted files from DOM
                audioBody.querySelectorAll('[data-file-id]').forEach(function (el) {
                    if (!serverAudioIds.has(parseInt(el.dataset.fileId, 10))) {
                        el.remove();
                        audioCount = Math.max(0, audioCount - 1);
                    }
                });
                coverRow.querySelectorAll('[data-file-id]').forEach(function (el) {
                    if (!serverCoverIds.has(parseInt(el.dataset.fileId, 10))) el.remove();
                });

                // Show/hide sections based on remaining content
                document.getElementById('audio-section').style.display =
                    audioBody.querySelectorAll('[data-file-id]').length ? '' : 'none';
                document.getElementById('cover-section').style.display =
                    coverRow.querySelectorAll('[data-file-id]').length ? '' : 'none';

                // Add new files
                files.forEach(function (file) {
                    if (file.id <= lastId) return;
                    lastId = file.id;
                    if (file.type === 'audio') {
                        document.getElementById('audio-section').style.display = '';
                        audioBody.appendChild(renderAudio(file));
                    } else if (file.type === 'cover') {
                        document.getElementById('cover-section').style.display = '';
                        coverRow.appendChild(renderCover(file));
                    }
                });
            })
            .catch(function () {});
    }

    setInterval(poll, 5000);
})();
</script>
<script>
(function () {
    const chatBody  = document.getElementById('chat-body');
    const pollUrl   = '{{ route('chat.index', $order) }}';
    let lastId      = 0;
    let autoScroll  = true;

    chatBody.addEventListener('scroll', function () {
        autoScroll = chatBody.scrollTop + chatBody.clientHeight >= chatBody.scrollHeight - 10;
    });

    function renderMessage(msg) {
        const wrap = document.createElement('div');
        wrap.className = 'mb-2 ' + (msg.is_admin ? 'text-start' : 'text-end');
        wrap.dataset.msgId = msg.id;

        const bubble = document.createElement('div');
        bubble.className = 'd-inline-block px-3 py-2 rounded-3 ' +
            (msg.is_admin ? 'bg-light border' : 'bg-primary text-white');
        bubble.style.maxWidth = '85%';
        bubble.innerHTML = '<p class="mb-0 small">' + escHtml(msg.body) + '</p>';

        const meta = document.createElement('div');
        meta.className = 'text-muted';
        meta.style.fontSize = '0.7rem';
        meta.textContent = (msg.is_admin ? 'Менеджер' : 'Вы') + ' · ' + msg.time;

        wrap.appendChild(bubble);
        wrap.appendChild(meta);
        return wrap;
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function initFromExisting() {
        chatBody.querySelectorAll('[data-msg-id]').forEach(function (el) {
            const id = parseInt(el.dataset.msgId, 10);
            if (id > lastId) lastId = id;
        });
    }

    function poll() {
        fetch(pollUrl + '?after=' + lastId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (messages) {
                if (!messages.length) return;

                // Remove "no messages yet" placeholder if present
                const empty = chatBody.querySelector('.text-center');
                if (empty) empty.remove();

                messages.forEach(function (msg) {
                    if (msg.id > lastId) {
                        chatBody.appendChild(renderMessage(msg));
                        lastId = msg.id;
                    }
                });

                if (autoScroll) chatBody.scrollTop = chatBody.scrollHeight;
            })
            .catch(function () { /* silently ignore network errors */ });
    }

    initFromExisting();
    if (autoScroll) chatBody.scrollTop = chatBody.scrollHeight;
    setInterval(poll, 4000);

    // Also poll immediately after the user submits a message
    const chatForm = document.querySelector('form[action="{{ route('chat.store', $order) }}"]');
    if (chatForm) {
        chatForm.addEventListener('submit', function () {
            setTimeout(poll, 800);
        });
    }
})();
</script>
@endpush
