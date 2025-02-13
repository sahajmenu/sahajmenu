<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StatusHistory extends Model
{
    /** @use HasFactory<\Database\Factories\StatusHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'reason',
        'status',
    ];

    protected $casts = [
        'status' => Status::class
    ];

    public function statusable(): MorphTo
    {
        return $this->morphTo();
    }
}
