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
     *      *
     * @return array<string, mixed>
     */
    public static $wrap = 'article';
    public function toArray(Request $request): array
    {
        $currentUser = Auth::user();
        $article = $this;
        $author = $article->author()->first();
        return [
                "author"=>  [
                  "bio"=>  $author->profile->bio,
                  "following"=>  $author->isFollowed($currentUser),
                  "image"=>  $author->profile->image,
                  "username"=>  $author->profile->username,
                ],
                "body"=>  $article->body,
                "createdAt"=>  $article->created_at,
                "description"=>  $article->description,
                "favorited"=>  $article->isFavorited($currentUser),
                "favoritesCount"=>  $article->favoritesCount(),
                "slug"=>  $article->date_slug,
                "tagList"=>  $article->getTagList(),
                "title"=>  $article->title,
                "updatedAt"=>  $article->updated_at
                ];
    }
}
