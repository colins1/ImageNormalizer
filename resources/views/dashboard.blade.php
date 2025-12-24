<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ImageNormalizer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">
<div class="max-w-3xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-semibold mb-6">ImageNormalizer</h1>

    @if (session('status'))
        <div class="mb-4 p-4 bg-emerald-900/50 border border-emerald-500/30 rounded-lg">
            <div class="flex items-center justify-between">
                <span>{{ session('status') }}</span>
                <a href="{{ route('batches.download', session('batch_token')) }}"
                    class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded text-sm font-medium">
                    📥 Скачать
                </a>
            </div>
        </div>
    @endif

    <p class="mb-4 text-sm text-slate-300">
        Сложи исходные изображения в папку <code>public/input</code>.  
        Результат появится в <code>public/output</code>.
    </p>

    <form method="POST" action="{{ route('images.process') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div class="mb-4">
            <span class="block text-sm text-slate-300 mb-1">Изображения (до 100 файлов)</span>
            <input type="file" name="images[]" multiple
                class="w-full text-sm text-slate-200 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-emerald-600 file:text-white file:cursor-pointer">
            @error('images')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
            @error('images.*')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm text-slate-300">Ширина (px)</span>
                <input type="number" name="width" value="{{ old('width', 1200) }}" min="1"
                       class="mt-1 w-full rounded-md bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                @error('width')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
            </label>

            <label class="block">
                <span class="text-sm text-slate-300">Высота (px)</span>
                <input type="number" name="height" value="{{ old('height', 800) }}" min="1"
                       class="mt-1 w-full rounded-md bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                @error('height')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm text-slate-300">Режим</span>
                <select name="mode"
                        class="mt-1 w-full rounded-md bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                    <option value="fit">fit (вписать)</option>
                    <option value="crop">crop (обрезать центр)</option>
                    <option value="resize">resize (пропорционально)</option>
                </select>
                @error('mode')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
            </label>

            <label class="block">
                <span class="text-sm text-slate-300">Формат</span>
                <select name="format"
                        class="mt-1 w-full rounded-md bg-slate-900 border border-slate-700 px-3 py-2 text-sm">
                    <option value="webp">webp</option>
                    <option value="jpg">jpg</option>
                    <option value="png">png</option>
                </select>
                @error('format')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
            </label>
        </div>

        <button type="submit"
                class="mt-2 px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-500 text-sm">
            Обработать изображения
        </button>
    </form>
</div>
</body>
</html>
