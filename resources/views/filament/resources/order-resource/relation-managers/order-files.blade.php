<div class="p-6 space-y-6">

    @if($audioFiles->isNotEmpty())
        <div>
            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Версии песни</h3>
            <div class="space-y-3">
                @foreach($audioFiles as $index => $file)
                    <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-4">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $file->label ?? 'Версия ' . ($index + 1) }}
                        </p>
                        <audio controls class="w-full">
                            <source src="{{ \Illuminate\Support\Facades\Storage::url($file->path) }}" type="audio/mpeg">
                        </audio>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($coverFiles->isNotEmpty())
        <div>
            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Обложки</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($coverFiles as $file)
                    <div>
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($file->path) }}"
                             class="rounded-xl w-full aspect-square object-cover border border-gray-200 dark:border-white/10"
                             alt="{{ $file->label }}">
                        @if($file->label)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">{{ $file->label }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($audioFiles->isEmpty() && $coverFiles->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">Файлы ещё не загружены.</p>
    @endif

</div>
