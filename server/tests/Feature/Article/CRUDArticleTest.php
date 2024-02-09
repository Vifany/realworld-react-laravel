<?php

use Illuminate\Http\Request;
use App\Models\{
    Profile,
    Article,
    Tag
};
use App\Http\Resources\JSON\{
    ArticleResource
};
use Illuminate\Support\Facades\Auth;

use function Pest\Faker\fake;

beforeEach(function () {
    $this->testUser = Profile::factory()->create()->user->first();
    $this->token = Auth::guard('api')->login($this->testUser);
    $this->testArticle = Article::factory()->create();
    Tag::factory(5)->create();
    $this->testArticle->tags()->attach(
        Tag::inRandomOrder()->limit(rand(2, 5))->get()
    );
    $this->articleArray =
        [
        'article' =>
        ArticleResource::make($this->testArticle)
            ->toArray(Request::create('/')),
        ];
    $this->changes = [
        "description" => fake()->sentence(),
        "body" => fake()->paragraphs(3, true),
    ];
    $this->articleReq =
        [
        "article" =>
            [
            "title" => fake()->word(),
            "description" => fake()->sentence(),
            "body" => fake()->paragraphs(3, true),
            "tagList" =>
                [
                fake()->word(),
                fake()->word(),
                ]
            ],
        ];

    $this->articleResp =
        [
        "article" =>
            [
            "title" => $this->articleReq["article"]["title"],
            "description" => $this->articleReq["article"]["description"],
            "body" => $this->articleReq["article"]["body"],
            "tagList" => $this->articleReq["article"]["tagList"],
            ],
        ];
    $this->articleTemp =
        [
        "article" =>
            [
            "slug",
            "title",
            "description",
            "body",
            "tagList",
            "createdAt",
            "updatedAt",
            "favorited",
            "favoritesCount",
            "author" =>
                [
                "username",
                "bio",
                "image",
                "following",
                ]
            ],
        ];
});


it('creates article', function () {
    // dd($this->articleTemp);
    $this->withHeaders(['Authorization' => "Token $this->token"])
        ->postJson(
            route('api.articles.store'),
            $this->articleReq
        )
        ->assertStatus(201)
        ->assertJson($this->articleResp)
        ->assertJsonStructure($this->articleTemp);
});

it('reads article by slug for non logged in user', function () {
    $this->withHeaders(['Authorization' => "Token $this->token"])
        ->getJson(
            route(
                'api.articles.read',
                ['slug' => $this->testArticle->date_slug]
            )
        )
        ->assertStatus(200)
        ->assertJsonStructure($this->articleTemp)
        ->assertJson($this->articleArray);
});

it('updates article by slug', function () {
    $this->withHeaders(['Authorization' => "Token $this->token"])
        ->putJson(
            route(
                'api.articles.update',
                ['slug' => $this->testArticle->date_slug]
            ),

        )
        ->assertStatus(200)
        ->assertJsonStructure($this->articleTemp)
        ->assertJson($this->articleArray);
});

it('deletes article by slug', function () {
    // $response = $this->get('/article/crudarticle');

    // $response->assertStatus(200);
});
