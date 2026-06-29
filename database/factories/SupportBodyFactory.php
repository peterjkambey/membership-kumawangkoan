<?php

namespace Database\Factories;

use App\Models\SupportBody;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportBodyFactory extends Factory
{
    protected $model = SupportBody::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company() . ' Corps',
            'description' => fake()->sentence(),
        ];
    }
}
