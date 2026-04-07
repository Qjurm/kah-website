<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'approved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved' => 'boolean',
        ];
    }

    public function instruments(): BelongsToMany
    {
        return $this->belongsToMany(Instrument::class, 'user_instrument')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    public function primaryInstrument(): ?Instrument
    {
        return $this->instruments()
                    ->wherePivot('is_primary', true)
                    ->first() ?? null;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isApproved(): bool
    {
        return (bool) $this->approved;
    }

    public function isMusician(): bool
    {
        return $this->role === 'musician' || $this->role === 'admin';
    }
}
