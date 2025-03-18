<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

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

        return sprintf('%s://%s.%s?table_id=%s', config('url.protocol'), $subdomain, config('url.short'), $this->id);
    }

    public function scopeGetClientOwnTable(Builder $query): void
    {
        $user = Auth::user();
        $query->when($user->clientAccess(), function ($query) use ($user): void {
            $query->where('client_id', $user->client->id);
        });
    }
}
