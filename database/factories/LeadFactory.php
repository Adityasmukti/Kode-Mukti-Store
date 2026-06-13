<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'download_token' => Str::random(48),
            'first_opted_at' => now(),
            'last_opted_at' => now(),
        ];
    }
}
