<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScorePart extends Model
{
    protected $fillable = ['score_id', 'instrument_id', 'instrument', 'part_number', 'pdf_path', 'original_filename'];

    public function score(): BelongsTo
    {
        return $this->belongsTo(Score::class);
    }

    public function instrument(): BelongsTo
    {
        return $this->belongsTo(Instrument::class);
    }
}
