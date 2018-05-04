<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Reservation extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => htmlspecialchars($this->id),
            'stuname' => htmlspecialchars($this->stuname),
            'stuno' => htmlspecialchars($this->stuno),
            'dorm' => htmlspecialchars($this->dorm),
            'contact' => htmlspecialchars($this->contact),
            'takeday' => htmlspecialchars($this->takeday),
            'taketime' => htmlspecialchars($this->taketime),
            'submited' => htmlspecialchars($this->submited),
            'updated' => htmlspecialchars($this->updated),
            'books' => []
        ];
    }
}
