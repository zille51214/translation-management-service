<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->slug,
            'locale' => $this->faker->randomElement(['en', 'fr', 'es']),
            'value' => $this->faker->sentence,
        ];
    }
}