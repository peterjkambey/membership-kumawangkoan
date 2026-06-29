<?php

namespace Database\Factories;

use App\Models\FamilyCard;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class FamilyCardFactory extends Factory
{
    protected $model = FamilyCard::class;

    public function definition(): array
    {
        return [
            'family_no' => fake()->unique()->numerify('KK-####-####'),
            'head_member_id' => null,
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'status' => 'active',
        ];
    }

    public function frozen(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'frozen']);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'inactive']);
    }

    public function withHead(): static
    {
        return $this->afterCreating(function (FamilyCard $card) {
            $head = Member::factory()->create([
                'family_card_id' => $card->id,
                'family_role' => 'head',
            ]);
            $card->head_member_id = $head->id;
            $card->save();
        });
    }
}
