<?php

use Illuminate\Http\Request;
use App\Models\{
    Profile,
    Article,
    Tag,
    User
};
use App\Http\Resources\JSON\{
    ArticleResource
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

use function Pest\Faker\fake;

beforeEach(function () {
    $this->testUser = Profile::factory(5)->create()->first()->user->first();
    $this->testArticle = Article::factory()->create(['author_id' => $this->testUser->id]);
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
            "tagList" => collect($this->articleReq["article"]["tagList"])->sort()->values()->toArray(),
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
    $this->actingAs($this->testUser);
    $this->postJson(
        route('api.articles.store'),
        $this->articleReq
    )

        ->assertStatus(201)
        ->assertJson($this->articleResp)
        ->assertJsonStructure($this->articleTemp);
});

it('reads article by slug for non logged in user', function () {
    $this->getJson(
        route(
            'api.articles.read',
            ['slug' => $this->testArticle->date_slug]
        )
    )
    ->assertStatus(200)
    ->assertJsonStructure($this->articleTemp)
    ->assertJson($this->articleArray);
});

it('updates article by slug', function ($field, $value) {
    $this->actingAs($this->testUser);
    $updData = [$field => $value];
    $chekData = $updData;
    if ($field == 'title') {
        $chekData = array_merge(
            $chekData,
            ['slug' => Str::slug($value) . '-' .$this->testArticle->updated_at->format('Y-m-d')]
        );
    }

    $this->putJson(
        route(
            'api.articles.update',
            ['slug' => $this->testArticle->date_slug]
        ),
        ['article' => [$field => $value]]
    )
        ->assertStatus(200)
        ->assertJsonStructure($this->articleTemp)
        ->assertJson(['article' => $chekData]);
})
->with(
    [
    'title' => ['field' => 'title', 'value' => fake()->word()],
    'description' => ['field' => 'description', 'value' => fake()->sentence()],
    'body' => ['field' => 'body', 'value' => fake()->paragraphs(3, true)],
    ]
);

it('refuses to update article by slug if not author', function () {
    $uData = ['article' => ['title' => 'pook']];
    $this->putJson(
        route(
            'api.articles.update',
            ['slug' => $this->testArticle->date_slug]
        ),
        $uData
    )
        ->assertStatus(401);
});

it('deletes article by slug', function () {
    $this->actingAs($this->testUser);
    $this->deleteJson(
        route(
            'api.articles.destroy',
            ['slug' => $this->testArticle->date_slug]
        )
    )
        ->assertStatus(204);
    expect(Article::find($this->testArticle->id))->toBeNull();
});

it('refuses to delete article if not author', function () {
    $this->deleteJson(
        route(
            'api.articles.destroy',
            ['slug' => $this->testArticle->date_slug]
        )
    )
    ->assertStatus(401);
    expect(Article::find($this->testArticle->id))->not()->toBeNull();
});
