<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Artisan;

class FailedJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'connection',
        'queue',
        'payload',
        'exception',
        'failed_at',
    ];

    protected $casts = [
        'failed_at' => 'datetime',
    ];

    public function retry()
    {
        try {
            Artisan::call('queue:retry', ['id' => $this->uuid]);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // $this->delete();
    }
}
