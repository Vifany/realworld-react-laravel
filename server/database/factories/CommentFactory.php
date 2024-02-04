<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userIds = User::pluck('id')->toArray();

        //Same spellcastery as in Article, but for article
        $article = Article::inRandomOrder()->first();
        $createdAt = $article->created_at;
        $offsetMinutes = rand(60, 7 * 24 * 60);
        $createdAtWithOffset = Carbon::parse($createdAt)->addMinutes($offsetMinutes);

        $randomMinutes = rand(0, 7 * 24 * 60);
        $createdAt = now()->subMinutes($randomMinutes);

        return [
            'author_id' => $this->faker->randomElement($userIds),
            'article_id' => $article,
            'body' => $this->faker->paragraph,
            'created_at' => $createdAtWithOffset,
        ];
    }
}
