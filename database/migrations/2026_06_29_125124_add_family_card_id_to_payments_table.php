<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('family_card_id')->nullable()->after('monthly_bill_id')
                ->constrained()->nullOnDelete();

            $table->index(['family_card_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['family_card_id', 'payment_date']);
            $table->dropForeign(['family_card_id']);
            $table->dropColumn('family_card_id');
        });
    }
};
