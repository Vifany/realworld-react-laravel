<?php

namespace Database\Factories;
use App\Models\Article;
use \App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {

        //Some highly powerful sorcerism which sets
        //created_at for article after author's created_at
        $author = User::inRandomOrder()->first();
        $createdAt = $author->created_at;
        $offsetMinutes = rand(60, 7 * 24 * 60);
        $createdAtWithOffset = Carbon::parse($createdAt)->addMinutes($offsetMinutes);

        return [
            'author_id' => $author,
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'body' => $this->faker->paragraphs(3, true),
            'created_at' => $createdAtWithOffset,
        ];
    }
}
