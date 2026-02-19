@php
    $record = $getRecord();
    $audioFiles = $record->audioFiles;
    $coverFiles = $record->coverFiles;
@endphp

@if($audioFiles->isNotEmpty())
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 mb-4">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Версии песни</h3>
        <div class="space-y-4">
            @foreach($audioFiles as $index => $file)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $file->label ?? 'Версия ' . ($index + 1) }}
                        </p>
                        <form action="{{ route('admin.order-files.destroy', $file) }}" method="POST"
                              onsubmit="return confirm('Удалить файл «{{ addslashes($file->label ?? 'Версия ' . ($index + 1)) }}»?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                Удалить
                            </button>
                        </form>
                    </div>
                    <audio controls class="w-full">
                        <source src="{{ \Illuminate\Support\Facades\Storage::url($file->path) }}" type="audio/mpeg">
                    </audio>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if($coverFiles->isNotEmpty())
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Обложки</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($coverFiles as $file)
                <div>
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($file->path) }}"
                         class="rounded-xl w-full aspect-square object-cover ring-1 ring-gray-950/5 dark:ring-white/10"
                         alt="{{ $file->label }}">
                    @if($file->label)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">{{ $file->label }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
