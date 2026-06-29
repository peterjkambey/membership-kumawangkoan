<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MembershipStatusLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipStatusLogFactory extends Factory
{
    protected $model = MembershipStatusLog::class;

    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'previous_status' => fake()->randomElement(['active', 'inactive', 'deceased']),
            'new_status' => fake()->randomElement(['active', 'inactive', 'deceased']),
            'reason' => fake()->sentence(),
            'changed_by' => User::factory(),
        ];
    }
}
