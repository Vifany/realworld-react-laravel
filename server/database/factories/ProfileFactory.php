<?php

namespace Database\Factories;

use App\Models\Profile;
use \App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition()
    {
        $user = User::factory()->create();

        return [
            'username' => $this->faker->unique()->userName(),
            'user_id' => $user->id,
            'bio' => $this->faker->paragraph,
            'image' => $this->faker->imageUrl(),
            'created_at' => $user->created_at,
        ];
    }
}
