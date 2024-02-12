<?php

use App\Models\Tag;

it('has tags/tags page', function () {
    Tag::factory(20)->create();
    $tags = Tag::orderBy('tag')->get()->pluck('tag')->toArray();

    $this->getJson(route('api.tags.get'))
        ->assertStatus(200)
        ->assertJson(
            ["tags" => $tags]
        );
});
