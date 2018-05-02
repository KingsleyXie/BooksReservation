<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserBook extends JsonResource
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
			'title' => $this->title,
			'author' => $this->author,
			'publisher' => $this->publisher,
			'pub_date' => $this->pub_date,
			'cover' => $this->cover,
			'quantity' => $this->quantity
		];
	}
}
