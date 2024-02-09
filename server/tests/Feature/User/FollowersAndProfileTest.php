<?php

use App\Models\{
    Profile,
    User
};

use Illuminate\Support\Arr;

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\{
    postJson,
    putJson,
    assertDatabaseHas,
    getJson,
    deleteJson,
};

beforeEach(function () {
    $this->userA = Profile::factory()->create()->user->first();
    $this->userB = Profile::factory()->create();
    $this->token = Auth::guard('api')->login($this->userA);
});

it('should be able to read profile', function () {
    $this->withHeaders(['Authorization' => "Token $this->token"])
        ->getJson(route(
            'api.profile.show',
            ['username' => $this->userB->username]
        ))
        ->assertStatus(200)
        ->assertJson(
            [
            'profile' => [
            'username' => $this->userB->username,
            'bio' => $this->userB->bio,
            'image' => $this->userB->image,
            ],
            ],
        );
});

it('should be able to follow profile', function () {
    $this->withHeaders(['Authorization' => "Token $this->token"])
        ->postJson(route(
            'api.profile.follow',
            ['username' => $this->userB->username]
        ))
        ->assertStatus(200)
        ->assertJson(
            [
            'profile' => [
            'username' => $this->userB->username,
            'bio' => $this->userB->bio,
            'image' => $this->userB->image,
            'following' => true,
            ],
            ],
        );
});

it('should be able to stop following profile', function () {
    $this->userA->follow(
        Profile::idByName($this->userB->username)
    );
    $this->withHeaders(['Authorization' => "Token $this->token"])
        ->deleteJson(route(
            'api.profile.unfollow',
            ['username' => $this->userB->username]
        ))
        ->assertStatus(200)
        ->assertJson(
            [
            'profile' => [
            'username' => $this->userB->username,
            'bio' => $this->userB->bio,
            'image' => $this->userB->image,
            'following' => false,
            ],
            ],
        );
});
