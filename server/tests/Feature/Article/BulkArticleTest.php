<?php

use App\Models\{
    Profile,
    Article,
    Tag
};

beforeEach(function () {
    $this->testUser = Profile::factory()->create()->first()->user->first();
    $this->testCeleb = Profile::factory()->create()->first()->user->first();
    $this->testArticleAuthor = Article::factory()->create(['author_id' => $this->testCeleb->id])->first();
    Article::factory(20)->create()->first();
});



it('able to get feed', function () {
    $this->actingAs($this->testUser);
    $this->testUser->follow($this->testCeleb);
    $response = $this->getJson(
        route('api.articles.feed')
    )
    ->assertJson(["articles" => []])
    ->assertStatus(200);
    $this->assertIsArray($response['articles']);

    $found = false;
    foreach ($response['articles'] as $item) {
        if (isset($item['slug']) && $item['slug'] === $this->testArticleAuthor->date_slug) {
            $found = true;
            break;
        }
    }

    $this->assertTrue($found, 'The desired slug not found in the array of JSON data.');
});

it('able to get index without filter', function () {

    $this->actingAs($this->testUser);
    $response = $this->getJson(
        route('api.articles.index'),
    )
    ->assertStatus(200)
    ->assertJsonStructure(
        [
            'articlesCount',
            'articles',
        ]
    )
    ->assertJson(['articlesCount' => 20]);
    $this->assertIsArray($response['articles']);
});

it('able to get index filtered by tag', function () {
    $testArticleTag = Article::factory()->create()->first();
    $testArticleTag->tags()->attach(Tag::firstOrCreate(['tag' => 'test_tag']));
    $this->actingAs($this->testUser);
    $response = $this->getJson(
        route(
            'api.articles.index',
            ['tag' => 'test_tag']
        ),
    )
    ->assertStatus(200);

    foreach ($response['articles'] as $item) {
        $this->assertContains('test_tag', $item['tagList']);
    }
});

it('able to get index filtered by author', function () {
    Article::factory()->create(['author_id' => $this->testCeleb->id])->first();

    $this->actingAs($this->testUser);
    $response = $this->getJson(
        route(
            'api.articles.index',
            ['author' => $this->testCeleb->profile->username]
        ),
    )
    ->assertStatus(200);
    foreach ($response['articles'] as $item) {
        $this->assertEquals($item['author']['username'], $this->testCeleb->profile->username);
    }
});

it('able to get index filtered by favorited', function () {
    $favArticle = Article::factory()->create()->first();
    $this->testUser->favorite($favArticle);

    $this->actingAs($this->testUser);
    $response = $this->getJson(
        route(
            'api.articles.index',
            ['favorited' => $this->testUser->profile->username]
        ),
    )
    ->assertStatus(200);
    foreach ($response['articles'] as $item) {
        $this->assertEquals($item['favorited'], true);
    }
});
