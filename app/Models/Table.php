<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Table extends Model
{
    /** @use HasFactory<\Database\Factories\TableFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'client_id',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getTableLinkAttribute(): string
    {
        $subdomain = $this->client->subdomain;

        return sprintf('%s://%s.%s?table_id=%s', config('app.schema'), $subdomain, config('app.short_url'), $this->id);
    }
}
