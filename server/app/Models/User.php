<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    //JWT


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    //Relations
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
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

    public function getFeed()
    {
        $feed = new Collection();
        foreach ($this->followings()->get() as $leader) {
            $feed = $feed->concat($leader->articles()->get());
        }
        return $feed;
    }

    public function follow($user)
    {
        $this->followings()->attach($user);
    }

    public function unfollow($user)
    {
        $this->followings()->detach($user);
    }

    public function isFollowing(?User $user)
    {
        if ($user != null) {
            return $this->followings->contains($user);
        }

        return false;
    }


    public function isFollowed(?User $user)
    {
        if ($user != null) {
            return $this->followers->contains($user);
        }

        return false;
    }

    public function favorite(Article $article)
    {
        if (!$this->hasFavorited($article)) {
            $this->favorites()->attach($article->id);
        };
    }

    public function unfavorite(Article $article)
    {
        if ($this->hasFavorited($article)) {
            $this->favorites()->detach($article->id, false);
        };
    }

    public function hasFavorited(Article $article)
    {
        return $this->favorites()->get()->contains($article);
    }

}
