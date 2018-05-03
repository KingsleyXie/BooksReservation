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
            'id' => $this->id,
            'stuname' => $this->stuname,
            'stuno' => $this->stuno,
            'dorm' => $this->dorm,
            'contact' => $this->contact,
            'takeday' => $this->takeday,
            'taketime' => $this->taketime,
            'submited' => $this->submited,
            'updated' => $this->updated,
            'books' => []
        ];
    }
}
