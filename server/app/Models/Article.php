<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'author',
        'title',
        'description',
        'body',
    ];

    //methods

    public function favoritesCount()
    {
        return $this->favorited()->count();
    }

    public function getTagList()
    {
        return $this->tags->pluck('tag')->toArray();
    }

    public function isFavorited(User $user)
    {
        return $this->favorited->contains($user);
    }


    //Relations
    public function author()
    {
        return $this->belongsTo(User::class, 'author');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    public function favorited()
    {
        return $this->belongsToMany(User::class, 'favorites', 'article_id', 'user_id')
            ->withTimestamps();
    }


}
