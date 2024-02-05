<?php

namespace App\Http\Resources\json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return ['profile' => [
            'username' => $this->profile->username,
            'bio' => $this->profile->bio,
            'image' => $this->profile->image,
            'following' => $this->following
        ]];
    }
}
