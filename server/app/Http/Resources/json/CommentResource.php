<?php

namespace App\Http\Resources\json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $author = $this->author()->first();
        return [
            "id"=> $this->id,
            "createdAt"=> $this->created_at,
            "updatedAt"=> $this->updated_at,
            "body"=> $this->body,
            "author"=> [
              "username"=> $author->profile->username,
              "bio"=> $author->profile->bio,
              "image"=> $author->profile->image,
              "following"=> $author->isFollowing(Auth::user())
            ],
        ];
    }
}
