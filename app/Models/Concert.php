<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Concert extends Model
{
    protected $fillable = ['title', 'date', 'location', 'is_current'];

    protected $casts = [
        'date' => 'date',
        'is_current' => 'boolean',
    ];

    public function scores(): BelongsToMany
    {
        return $this->belongsToMany(Score::class);
    }
}
