<?php

namespace Database\Factories;

use App\Models\FamilyCard;
use App\Models\PaymentGatewayTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentGatewayTransactionFactory extends Factory
{
    protected $model = PaymentGatewayTransaction::class;

    public function definition(): array
    {
        return [
            'gateway' => 'xendit',
            'channel' => fake()->randomElement(['VA', 'QRIS']),
            'external_id' => fake()->unique()->numerify('INV-####'),
            'gateway_transaction_id' => 'xendit-' . fake()->unique()->uuid(),
            'family_card_id' => FamilyCard::factory(),
            'amount' => 20000,
            'paid_amount' => 0,
            'status' => 'PENDING',
            'bank_code' => 'BCA',
            'account_number' => '8800' . fake()->numerify('######'),
            'expires_at' => now()->addDay(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'PAID',
            'paid_amount' => $attrs['amount'] ?? 20000,
            'paid_at' => now(),
        ]);
    }

    public function va(): static
    {
        return $this->state(fn (array $attrs) => [
            'channel' => 'VA',
            'bank_code' => fake()->randomElement(['BCA', 'BNI', 'BRI', 'MANDIRI']),
            'account_number' => '8800' . fake()->numerify('######'),
        ]);
    }

    public function qris(): static
    {
        return $this->state(fn (array $attrs) => [
            'channel' => 'QRIS',
            'qr_string' => '00020101021126580010IDXENDIT' . fake()->uuid(),
        ]);
    }
}
