<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];


    //Relations

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'author');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'author');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'following', 'user_id', 'follower_user_id')
                    ->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'following', 'follower_user_id', 'user_id')
                    ->withTimestamps();
    }

    public function favorites()
    {
        return $this->belongsToMany(Article::class, 'favorites', 'user_id', 'article_id')
                    ->withTimestamps();
    }


    //Methods
    public function follow(User $user)
    {
        $this->followings()->attach($user->id);
    }

    public function unfollow(User $user)
    {
        $this->followings()->detach($user->id);
    }

    public function isFollowing(User $user)
    {
        return $this->followings->contains($user);
    }

    public function favorite(Article $article)
    {
        $this->favorites()->attach($article->id);
    }

    public function unfavorite(Article $article)
    {
        $this->favorites()->detach($article->id);
    }

    public function hasFavorited(Article $article)
    {
        return $this->favorites->contains($article);
    }

}
