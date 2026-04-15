<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Instrument extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'display_order'];

    public function aliases(): HasMany
    {
        return $this->hasMany(InstrumentAlias::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_instrument')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    public function parts(): HasMany
    {
        return $this->hasMany(ScorePart::class);
    }
}
