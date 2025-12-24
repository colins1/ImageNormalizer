<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'token',
        'user_id',
        'status',
        'files_count',
        'total_size',
        'mode',
        'format',
        'archive_path',
        'error',
    ];
}
