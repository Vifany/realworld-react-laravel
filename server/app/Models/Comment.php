<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'article_id',
        'body',
    ];


    //Relations
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    //Methods

    public function isAuthor(User $user): bool
    {
        return $this->author_id == $user->id;
    }
}
