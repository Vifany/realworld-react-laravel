<?php

namespace App\Http\Resources\json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public static $wrap = null;
    public function toArray(Request $request): array
    {
        $currentUser = $this->user;
        $article = $this->article;
        $author = $article->author()->first();
        return [
            "article"=> [
                "author"=>  [
                  "bio"=>  $author->profile->bio,
                  "following"=>  $author->isFollowing($currentUser),
                  "image"=>  $author->profile->image,
                  "username"=>  $author->profile->username,
                ],
                "body"=>  $article->body,
                "createdAt"=>  $article->created_at,
                "description"=>  $article->description,
                "favorited"=>  $article->isFavorited($currentUser),
                "favoritesCount"=>  $article->favoritesCount(),
                "slug"=>  $article->id,
                "tagList"=>  $article->getTagList(),
                "title"=>  $article->title,
                "updatedAt"=>  $article->updated_at
                ]
        ];
    }
}
