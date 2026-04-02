<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Concert extends Model
{
    protected $fillable = ['title', 'date', 'location', 'photo_path', 'is_current', 'is_public'];

    protected $casts = [
        'date' => 'date',
        'is_current' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function scores(): BelongsToMany
    {
        return $this->belongsToMany(Score::class);
    }
}
