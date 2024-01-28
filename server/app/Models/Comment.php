<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'author',
        'article',
        'body',
    ];


    //Relations
    public function author()
    {
        return $this->belongsTo(User::class, 'author');
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
