<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Скачать результат обработки</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">
<div class="max-w-md mx-auto py-12 px-4">
    <div class="bg-slate-800 rounded-xl p-8 shadow-2xl border border-slate-700">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-emerald-500 rounded-full mx-auto flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold mb-2">Готово!</h1>
            <p class="text-slate-400">Батч #{{ $batch->id }}</p>
        </div>

        <div class="space-y-4 mb-8">
            <div class="bg-emerald-900/50 border border-emerald-500/30 rounded-lg p-4">
                <p class="text-sm text-emerald-200">
                    ✅ Обработано файлов: <strong>{{ $batch->files_count }}</strong>
                </p>
                <p class="text-sm text-emerald-200">
                    📁 Формат: <strong>{{ ucfirst($batch->format) }}</strong>
                </p>
                <p class="text-sm text-emerald-200">
                    🎨 Режим: <strong>{{ ucfirst($batch->mode) }}</strong>
                </p>
            </div>

            <div class="bg-amber-900/50 border border-amber-500/30 rounded-lg p-4">
                <h3 class="font-semibold text-amber-200 mb-2">⚠️ Важно!</h3>
                <p class="text-sm text-amber-200">
                    После скачивания архива <strong>файлы будут удалены навсегда</strong>. 
                    Сохраните результат на свой компьютер.
                </p>
            </div>
        </div>

        <div class="space-y-3">
            <a href="{{ $downloadUrl }}"
               class="w-full block bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-3 px-6 rounded-lg text-center transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                📥 Скачать архив ({{ number_format($batch->files_count) }} файлов)
            </a>
            
            <a href="{{ route('images.index') }}"
               class="w-full block bg-slate-700 hover:bg-slate-600 text-slate-200 font-medium py-3 px-6 rounded-lg text-center transition-all duration-200">
                ← Назад к обработке
            </a>
        </div>
    </div>
</div>
</body>
</html>
