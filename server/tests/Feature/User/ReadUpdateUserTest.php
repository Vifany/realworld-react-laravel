<?php

use App\Models\{
    Profile,
    User
};

use Illuminate\Support\Arr;

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Hash;
use function Pest\Faker\fake;
use function Pest\Laravel\{
    postJson,
    putJson,
    assertDatabaseHas,
};

const API_USERS_GET = 'api.user.get';
const API_USERS_UPDATE = 'api.user.update';




beforeEach(
    function () {
        $this->testArray = (object) [
        "user" =>
        (object) [
            "username" => fake()->word(),
            "email" => fake()->email(),
            "password" => fake()->password(17, 24),
        ],
        ];
        $this->testUser =
            User::factory()
                ->withUsername($this->testArray->user->username)
                ->create(
                [
                    'email' => $this->testArray->user->email,
                    'password' => Hash::make($this->testArray->user->password),
                    ]
                );
            $this->token = Auth::guard('api')->login($this->testUser);
    }
);

it('should receive logged in user data', function () use ($testArray) {


    $this->withHeaders(['Authorization' => "Token $this->token"])
        ->get(route(API_USERS_GET))
        ->assertStatus(200)
        ->assertJson(
            [
            'user' => [
            'email' => $this->testUser->email,
            'username' => $this->testUser->profile->username,
            'bio' => $this->testUser->profile->bio,
            'image' => $this->testUser->profile->image,
            ],
            ],
        );
});

it('should update logged in user', function () use ($testArray) {

    $updateArray = [
        "user" =>
        [
          "username" => fake()->word(),
          "email" => fake()->email(),
          "password" => fake()->password(17, 24),
          "bio" => fake()->sentence(),
        ],
    ];

    $response = putJson(
        route(API_USERS_UPDATE),
        $updateArray,
        ['Authorization' => "Token $this->token"]
    );


    $response->assertStatus(200)
    ->assertJson(
        [
        'user' => [
        'email' => $updateArray["user"]['email'],
        'username' => $updateArray["user"]['username'],
        'bio' => $updateArray["user"]['bio'],
        ],
        ],
    );
});
