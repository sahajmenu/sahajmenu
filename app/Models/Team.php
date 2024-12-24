<?php

namespace App\Models;

use App\Models\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([TeamScope::class])]
class Team extends Model
{
    protected $fillable = [
        'name',
        'domain',
        'slug',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
