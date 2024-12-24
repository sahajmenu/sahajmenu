<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'team_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted(): void
    {
        $user = Auth::user();
        if (! $user->is_admin) {
            static::addGlobalScope('team_user', function (Builder $builder) use ($user) {
                $builder->where('team_id', $user->team_id);
            });
        }
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role === Role::ADMINISTRATOR;
    }

    public function scopeTeamFilterRoles(Builder $query): void
    {
        $user = Auth::user();
        $query->when(! $user->is_admin, function ($query) use ($user) {
            $query->when($user->role === Role::OWNER, function ($query) {
                $query->whereIn('role', Role::getOwnerRoles());
            });
            $query->when($user === Role::MANAGER, function ($query) {
                $query->whereIn('role', Role::getManagerRoles());
            });
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }
}
