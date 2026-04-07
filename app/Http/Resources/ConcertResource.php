<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConcertResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'date' => $this->date?->format('Y-m-d'),
            'location' => $this->location,
            'photo_path' => $this->photo_path,
            'is_current' => $this->is_current,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'scores' => ScoreResource::collection($this->whenLoaded('scores')),
        ];
    }
}
