<?php

namespace Database\Factories;

use App\Models\Benefit;
use Illuminate\Database\Eloquent\Factories\Factory;

class BenefitFactory extends Factory
{
    protected $model = Benefit::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->lexify('BENEFIT-???'),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'icon' => 'heroicon-o-check-badge',
            'sort_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
