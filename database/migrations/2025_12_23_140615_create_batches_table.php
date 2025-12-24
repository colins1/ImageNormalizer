<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();       // публичный ID для пользователя
            $table->unsignedBigInteger('user_id')->nullable(); // если будет авторизация
            $table->enum('status', ['queued', 'processing', 'done', 'failed'])
                ->default('queued');
            $table->unsignedInteger('files_count')->default(0);
            $table->unsignedBigInteger('total_size')->default(0); // в байтах
            $table->string('mode');      // fit/crop/resize
            $table->string('format');    // webp/jpg/png
            $table->string('archive_path')->nullable(); // путь к .zip с результатом
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
