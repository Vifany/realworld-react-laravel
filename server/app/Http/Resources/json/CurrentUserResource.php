<?php

namespace App\Http\Resources\json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public static $wrap = null;
    public function toArray($request): array
    {

        return  ['user' => [
            'email' => $this-> user->email,
            'token' => $this->token,
            'username' => $this->user->profile->username,
            'bio' => $this->user->profile->bio,
            'image' => $this->user->profile->image,
        ]];
    }
}
