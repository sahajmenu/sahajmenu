<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'subdomain',
        'slug',
        'logo',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function scopeGetOwnClient(Builder $query): void
    {
        $user = Auth::user();
        $query->when($user?->clientAccess(), function ($query) use ($user) {
            $query->where('id', $user->client_id);
        });
    }

    public function scopeSubdomain(Builder $query, string $subdomain): void
    {
        $query->where('subdomain', $subdomain);
    }
}
