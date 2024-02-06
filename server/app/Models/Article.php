<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'title',
        'description',
        'body',
        'date_slug',
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

    public function isAuthor(User $user): bool
    {
        return $this->author_id == $user->id;
    }

    public function isFavorited(?User $user)
    {
        if ($user != null) {
            return $this->favorited->contains($user);
        }
        return false;
    }

    //Relations
    public function author()
    {
        return $this->belongsTo(User::class);
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

    //Medjiq

    /**
     * Save with modifications
     *
     * @return parent
     */
    public function save(array $options = [])
    {

        $this->generateSlug();
        return parent::save($options);
    }

    /**
     * Function to generate and save dat slug
     *
     * @return void
     */
    protected function generateSlug()
    {
        $createdAtDate = Carbon::now()->format('Y-m-d');
        $slug = Str::slug($this->title) . '-' . $createdAtDate;
        if (self::where('date_slug', $slug)->exists()) {
            $count = 0;
            $slog = $slug;
            while (self::where('date_slug', $slug)->exists()) {
                $count++;
                $slug = $count . '-' . $slog;
            }
        }

        $this->date_slug = $slug;
    }


    //Scopes

    public function scopeTagged(Builder $query, $tag)
    {
        if ($tag == null) {
            return $query;
        }

        return $query->whereHas(
            'tags',
            function ($query) use ($tag) {
                $query->where('tag', $tag);
            }
        );
    }

    public function scopeWrittenBy(Builder $query, $author_id)
    {
        if ($author_id == null) {
            return $query;
        }

        return $query->where('author_id', $author_id);
    }

    public function scopeFavoritedBy(Builder $query, $user_id)
    {
        if ($user_id == null) {
            return $query;
        }

        return $query->whereHas(
            'favorited',
            function ($query) use ($user_id) {
                $query->where(
                    'user_id',
                    $user_id
                );
            }
        );
    }

    public function scopeSlugged(Builder $query, $slug)
    {
        return $query->where('date_slug', $slug);
    }
}
