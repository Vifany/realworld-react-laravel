<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'author',
        'topic',
        'description',
        'body',
    ];


    //Relations
    public function author()
    {
        return $this->belongsTo(User::class, 'author');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'author');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'article_id', 'user_id')
                    ->withTimestamps();
    }
}
