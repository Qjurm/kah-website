<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScoreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'composer' => $this->composer,
            'arranger' => $this->arranger,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parts' => ScorePartResource::collection($this->whenLoaded('parts')),
        ];
    }
}
