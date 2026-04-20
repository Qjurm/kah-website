<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstrumentSection extends Model
{
    protected $fillable = ['name', 'display_order'];

    public function instruments()
    {
        return $this->hasMany(Instrument::class, 'section_id');
    }
}
