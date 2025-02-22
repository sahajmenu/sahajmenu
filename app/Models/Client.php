<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Plan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'subdomain',
        'logo',
        'expires_at',
        'plan'
    ];

    protected $casts = [
        'plan' => Plan::class
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function scopeSubdomain(Builder $query, string $subdomain): void
    {
        $query->where('subdomain', $subdomain);
    }

    public function status(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'statusable');
    }

    public function latestStatus(): MorphOne
    {
        return $this->morphOne(StatusHistory::class, 'statusable')->latestOfMany();
    }
}
