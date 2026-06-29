<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_cards', function (Blueprint $table) {
            $table->decimal('monthly_dues', 12, 2)->default(20000.00)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('family_cards', function (Blueprint $table) {
            $table->dropColumn('monthly_dues');
        });
    }
};
