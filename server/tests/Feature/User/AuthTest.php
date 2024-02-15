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
    assertDatabaseHas,
};

const API_USERS_CREATE = 'api.users.register';
const API_USERS_LOGIN = 'api.users.login';

$testArray = (object) [
    "user" =>
    (object) [
      "username" => fake()->word(),
      "email" => fake()->email(),
      "password" => fake()->password(17, 24),
    ],
    ];

it('able to register new user', function () use ($testArray) {

    postJson(route(API_USERS_CREATE), (array) $testArray)
        ->assertStatus(200)
        ->assertJson(
            [
            'user' => [
                'email' => $testArray->user->email,
                'username' => $testArray->user->username,
                ],
            ]
        )
        ->assertJson(
            fn(AssertableJson $json) => $json->whereType('user.token', 'string')
        );
    assertDatabaseHas('users', ['email' => $testArray->user->email]);
    assertDatabaseHas('profiles', ['username' => $testArray->user->username]);
});


it('should not allow registration of the same user twice', function () {
    $rawUserData = Profile::factory()->create();
    Arr::set($rawUserData, 'password', fake()->password());

    postJson(
        route(API_USERS_CREATE),
        [
            'user' => [
            'email' => $rawUserData->user->email,
            'username' => $rawUserData->user->profile->username,
            'password' => $rawUserData->password,
            ],
        ]
    )
        ->assertStatus(422)
        ->assertJson(
            fn(AssertableJson $json) => $json->where(
                'errors.body',
                'The user.username has already been taken. The user.email has already been taken.'
            )
        );
});

it('should log in user and get data data', function () use ($testArray) {
    $user = User::factory()
        ->withUsername($testArray->user->username)
        ->create(
            [
                'email' => $testArray->user->email,
                'password' => Hash::make($testArray->user->password),
            ]
        );

    postJson(
        route(API_USERS_LOGIN),
        [
            'user' =>
            [
                'email' => $testArray->user->email,
                'password' => $testArray->user->password,
            ],
        ]
    )
    ->assertStatus(200)
    ->assertJson(
        [
            'user' => [
                'email' => $user->email,
                'username' => $user->profile->username,
                'bio' => $user->profile->bio,
                'image' => $user->profile->image,
            ],
        ],
    );
});
