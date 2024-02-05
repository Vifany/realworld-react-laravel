<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'user_id',
        'bio',
        'image',
    ];
    protected $table = 'profiles';

    //Relations

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    //Methods

    public static function idByName($username)
    {
        $profile = self::where('username', $username)->first();
        return $profile ? $profile->user_id : null;
    }
}
