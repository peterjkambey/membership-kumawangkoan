<?php

namespace Database\Factories;

use App\Models\MemberMembership;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberMembershipFactory extends Factory
{
    protected $model = MemberMembership::class;

    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'membership_number' => fake()->unique()->numerify('M-####-####'),
            'start_date' => fake()->date(),
            'end_date' => fake()->optional()->date(),
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'inactive']);
    }
}
