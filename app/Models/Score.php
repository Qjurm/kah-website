<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Score extends Model
{
    protected $fillable = ['title', 'composer', 'arranger'];

    public function parts(): HasMany
    {
        return $this->hasMany(ScorePart::class);
    }

    public function concerts(): BelongsToMany
    {
        return $this->belongsToMany(Concert::class);
    }
}
