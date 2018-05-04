<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminBook extends JsonResource
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
			'title' => htmlspecialchars($this->title),
			'author' => htmlspecialchars($this->author),
			'publisher' => htmlspecialchars($this->publisher),
			'pubdate' => htmlspecialchars($this->pubdate),
			'cover' => htmlspecialchars($this->cover),
			'quantity' => htmlspecialchars($this->quantity),
			'imported' => htmlspecialchars($this->imported),
			'updated' => htmlspecialchars($this->updated)
		];
	}
}
