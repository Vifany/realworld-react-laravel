<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Tag;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Article::factory()->count(21)->create()->each(function ($article) {
            $tags = Tag::inRandomOrder()->limit(rand(2, 5))->get();
            $article->tags()->attach($tags);
        });
    }
}
