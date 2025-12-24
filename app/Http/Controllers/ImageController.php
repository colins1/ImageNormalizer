<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Models\Batch;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function index()
    {
        // пока просто показываем форму без списка файлов
        return view('dashboard');
    }

    public function process(Request $request)
    {
        $data = $request->validate([
            'width'   => 'required|integer|min:1',
            'height'  => 'required|integer|min:1',
            'mode'    => 'required|in:fit,crop,resize',
            'format'  => 'required|in:webp,jpg,png',
            'images.*' => 'required|image|max:5120', // до 5 МБ на файл
        ]);

        $files = $request->file('images', []);
        if (count($files) === 0) {
            return back()->withErrors(['images' => 'Загрузите хотя бы один файл.']);
        }

        if (count($files) > 100) {
            return back()->withErrors(['images' => 'Максимум 100 файлов за один раз.']);
        }

        // считаем общий размер
        $totalSize = array_sum(array_map(fn ($f) => $f->getSize(), $files));

        // создаём батч
        $batch = Batch::create([
            'token'       => Str::uuid()->toString(),
            'user_id'     => null, // потом заменим на реального пользователя
            'status'      => 'processing',
            'files_count' => count($files),
            'total_size'  => $totalSize,
            'mode'        => $data['mode'],
            'format'      => $data['format'],
        ]);

        // директории под этот батч
        $baseDir   = storage_path('app/batches/' . $batch->id);
        $inputDir  = $baseDir . '/input';
        $outputDir = $baseDir . '/output';

        File::makeDirectory($inputDir, 0755, true, true);
        File::makeDirectory($outputDir, 0755, true, true);

        // сохраняем загруженные файлы во входную папку
        foreach ($files as $file) {
            $file->move($inputDir, $file->getClientOriginalName());
        }

        // тут пока СИНХРОННО обрабатываем (потом вынесем в Job)
        $processed = 0;
        $inputFiles = File::allFiles($inputDir);

        foreach ($inputFiles as $file) {
            $image = Image::make($file->getRealPath());

            switch ($data['mode']) {
                case 'fit':
                    $image->fit($data['width'], $data['height']);
                    break;
                case 'crop':
                    $image->crop($data['width'], $data['height'], null, null);
                    break;
                case 'resize':
                    $image->resize($data['width'], $data['height'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    break;
            }

            $nameWithoutExt = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $newName = $nameWithoutExt . '.' . $data['format'];

            $savePath = $outputDir . DIRECTORY_SEPARATOR . $newName;

            $image->encode($data['format']);
            $image->save($savePath);

            $processed++;
        }

        // Создаём ZIP архив из обработанных файлов
        $archivePath = $baseDir . '/result.zip';
        $zip = new \ZipArchive();

        if ($zip->open($archivePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $outputFiles = File::allFiles($outputDir);
            
            foreach ($outputFiles as $file) {
                $relativeName = basename($file->getFilename());
                $zip->addFile($file->getRealPath(), $relativeName);
            }
            
            $zip->close();
            
            // сохраняем путь к архиву в батч
            $batch->archive_path = 'batches/' . $batch->id . '/result.zip';
        } else {
            // если не удалось создать архив
            $batch->status = 'failed';
            $batch->error = 'Не удалось создать архив результата';
            $batch->save();
            
            return back()->with('error', 'Обработка завершена, но не удалось создать архив.');
        }

        

        $batch->status = 'done';
        $batch->save();

        return back()
            ->with('status', "Батч #{$batch->id} обработан, файлов: {$processed}")
            ->with('batch_token', $batch->token);
    }


    public function download(Request $request, $token)
    {
        $batch = Batch::where('token', $token)
                    ->where('status', 'done')
                    ->firstOrFail();

        $archivePath = storage_path('app/' . $batch->archive_path);

        if (!File::exists($archivePath)) {
            abort(404, 'Архив не найден');
        }

        // если пользователь подтвердил скачивание
        if ($request->has('confirm')) {
            // отдаём файл и помечаем батч для удаления
            $batch->status = 'downloaded'; // добавь этот статус в enum миграции
            $batch->save();
            
            return response()->download($archivePath)->deleteFileAfterSend(true);
        }

        // показываем предупреждение
        $downloadUrl = route('batches.download', $token) . '?confirm=1';
        
        return view('batches.warning', compact('batch', 'downloadUrl'));
    }


}
