<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement(['petugas', 'guru', 'orangtua']),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function petugas(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'petugas']);
    }

    public function guru(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'guru']);
    }

    public function orangtua(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'orangtua']);
    }
}
