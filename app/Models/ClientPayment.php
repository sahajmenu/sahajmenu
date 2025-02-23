<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPayment extends Model
{
    protected $fillable = [
        'client_id',
        'type',
        'statement',
        'actioned_by',
        'note',
        'amount'
    ];

    protected $casts = [
        'type' => PaymentType::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }
}
