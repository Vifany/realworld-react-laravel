<?php
use App\Models\{
    Profile,
    Article,
    Comment
};

beforeEach(function () {
    $this->testUser = Profile::factory()->create()->first()->user()->first();
    $this->testArticle = Article::factory()->create();
});

it('able to comment an article', function () {
    $this->actingAs($this->testUser);
    $testCommentBody = ['comment' => ['body' => 'test_comment']];
    $this->postJson(
        route(
            'api.articles.comments.store',
            ['slug' => $this->testArticle->date_slug]
        ),
        $testCommentBody
    )
        ->assertStatus(200)
        ->assertJson($testCommentBody)
        ->assertJsonStructure(
            [
            'comment' => [
                "id",
                "createdAt",
                "updatedAt",
                "body",
                "author" => [
                    "username",
                    "bio",
                    "image",
                    "following",
                    ],
                ],
            ]
        );
});

it('able to read comments for an article', function () {
    $this->actingAs($this->testUser);
    $testCommentBody = [
        'body' => 'test_comment',
        'author_id' => $this->testUser->id,
    ];
    $this->testArticle->comments()->create($testCommentBody);
    $response = $this->getJson(
        route(
            'api.articles.comments.read',
            ['slug' => $this->testArticle->date_slug]
        )
    )
    ->assertStatus(200)
    ->assertJsonStructure(['comments']);
    $found = false;
    foreach (($response->json())['comments'] as $item) {
        if ($item['body'] == $testCommentBody['body']) {
            $found = true;
            break;
        }
    }
    $this->assertTrue($found, 'The test_comment not found in the array of JSON data.');

});

it('able to remove comment of an article by id', function () {
    $this->actingAs($this->testUser);
    $testCommentBody = [
        'body' => 'test_comment',
        'author_id' => $this->testUser->id,
    ];
    $this->testArticle->comments()->create($testCommentBody);
    $testComment = $this->testArticle->comments()->first();
    $response = $this->deleteJson(
        route(
            'api.articles.comments.destroy',
            [
                'slug' => $this->testArticle->date_slug,
                'id' => $testComment->id,
            ]
        )
    )
    ->assertStatus(200);
    expect(Comment::find($testComment->id))->toBeNull();

});

it('refuse to delete comment if user is not author of it', function () {
    $testCommentBody = [
        'body' => 'test_comment',
        'author_id' => $this->testUser->id,
    ];
    $this->testArticle->comments()->create($testCommentBody);
    $testComment = $this->testArticle->comments()->first();
    $this->deleteJson(
        route(
            'api.articles.comments.destroy',
            [
                'slug' => $this->testArticle->date_slug,
                'id' => $testComment->id,
            ]
        )
    )
    ->assertStatus(401);
    expect(Comment::find($testComment->id))->not()->toBeNull();
});
