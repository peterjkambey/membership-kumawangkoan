<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('card_uid', 50)->unique()->nullable()->after('photo');
            $table->date('card_issued_at')->nullable()->after('card_uid');
            $table->enum('card_status', ['none', 'issued', 'lost', 'replaced'])
                ->default('none')->after('card_issued_at');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['card_uid', 'card_issued_at', 'card_status']);
        });
    }
};
