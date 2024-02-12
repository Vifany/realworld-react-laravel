<?php

use App\Models\{
    Profile,
    Article
};



beforeEach(function () {
    $this->testUser = Profile::factory()->create()->first()->user->first();
    $this->testArticle = Article::factory()->create()->first();
    $this->slug = ['slug' => $this->testArticle->date_slug];
});


it('able to add favorites', function () {
    $this->actingAs($this->testUser);
    $this->postJson(
        route(
            'api.articles.favorite',
            $this->slug
        )
    )
        ->assertStatus(200);

    $this->assertDatabaseHas(
        'favorites',
        [
            'article_id' => $this->testArticle->id,
            'user_id' => $this->testUser->id,
        ]
    );
});

it('able to get feed', function () {
    $this->actingAs($this->testUser);
    $this->testUser->favorite($this->testArticle);
    $this->getJson(
        route('api.articles.feed')
    )
    ->assertJson(["articles" => []])
    ->assertStatus(200);
});

it('able to remove favorites', function () {

    $this->actingAs($this->testUser);
    $this->testUser->favorite($this->testArticle);
    $this->deleteJson(
        route(
            'api.articles.unfavorite',
            ['slug' => $this->testArticle->date_slug]
        )
    )->assertStatus(200);

    $this->assertDatabaseMissing(
        'favorites',
        [
            'article_id' => $this->testArticle->id,
            'user_id' => $this->testUser->id,
        ]
    );
});

//Le sorserismus fonctionnel grande

it('refuse to operate with favorites without logging in', function ($method, $route) {
    $this->{$method}(
        route($route, $this->slug)
    )
        ->assertStatus(401);
})->with([
    "add" => [
        'method' => 'postJson',
        'route' => 'api.articles.favorite'
    ],
    "remove" => [
        'method' => 'deleteJson',
        'route' => 'api.articles.unfavorite'
    ],
    "feed" => [
        'method' => 'getJson',
        'route' => 'api.articles.feed'
    ],
]);
