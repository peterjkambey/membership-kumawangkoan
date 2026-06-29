<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateway_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->default('xendit');            // xendit, midtrans, dll
            $table->string('channel', 20);                            // VA, QRIS
            $table->string('external_id')->unique();                  // ID unik dari kita
            $table->string('gateway_transaction_id')->nullable();     // ID dari Xendit
            $table->foreignId('family_card_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('status')->default('PENDING');             // PENDING, PAID, EXPIRED, FAILED
            $table->string('bank_code')->nullable();                  // BCA, BNI, MANDIRI, BRI
            $table->string('account_number')->nullable();             // Nomor VA
            $table->text('qr_string')->nullable();                    // QRIS content
            $table->json('gateway_response')->nullable();             // Raw response dari Xendit
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['family_card_id', 'status']);
            $table->index(['gateway', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_transactions');
    }
};
