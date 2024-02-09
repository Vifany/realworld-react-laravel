<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomMinutes = rand(0, 7 * 24 * 60);
        $createdAt = now()->subMinutes($randomMinutes);

        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password123456789'),
            'created_at' => $createdAt,
        ];
    }

    public function withUsername(string $username): self
    {
        return $this->afterCreating(function (User $user) use ($username) {
            $user->profile()->create(
                [
                'username' => $username,
                ]
            );
        });
    }
}
