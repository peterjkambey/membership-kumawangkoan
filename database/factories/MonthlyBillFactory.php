<?php

namespace Database\Factories;

use App\Models\FamilyCard;
use App\Models\MonthlyBill;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonthlyBillFactory extends Factory
{
    protected $model = MonthlyBill::class;

    public function definition(): array
    {
        return [
            'family_card_id' => FamilyCard::factory(),
            'period' => fake()->date('Y-m'),
            'amount' => fake()->randomFloat(2, 50000, 500000),
            'status' => 'unpaid',
            'due_date' => fake()->date(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'paid']);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'overdue',
            'due_date' => fake()->date(max: '-1 day'),
        ]);
    }
}
