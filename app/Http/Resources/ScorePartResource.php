<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScorePartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'score_id' => $this->score_id,
            'instrument' => $this->instrument,
            'pdf_path' => $this->pdf_path,
            'created_at' => $this->created_at,
        ];
    }
}
