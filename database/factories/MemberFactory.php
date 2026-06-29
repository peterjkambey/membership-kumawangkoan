<?php

namespace Database\Factories;

use App\Models\FamilyCard;
use App\Models\Member;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'nik' => fake()->unique()->numerify('################'),
            'full_name' => fake()->name(),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_date' => fake()->date(max: '-17 years'),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'family_card_id' => null,
            'region_id' => Region::factory(),
            'membership_number' => fake()->unique()->numerify('KMN-####'),
            'join_date' => fake()->date(),
            'family_role' => fake()->randomElement(['head', 'spouse', 'child']),
            'status' => 'active',
        ];
    }

    public function head(): static
    {
        return $this->state(fn (array $attrs) => ['family_role' => 'head']);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'inactive']);
    }
}
