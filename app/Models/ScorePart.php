<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScorePart extends Model
{
    protected $fillable = ['score_id', 'instrument', 'pdf_path'];

    public function score(): BelongsTo
    {
        return $this->belongsTo(Score::class);
    }
}
