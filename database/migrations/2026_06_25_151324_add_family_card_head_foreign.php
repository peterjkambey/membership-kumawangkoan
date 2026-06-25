<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_cards', function (Blueprint $table) {
            $table->foreign('head_member_id')->references('id')->on('members')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('family_cards', function (Blueprint $table) {
            $table->dropForeign(['head_member_id']);
        });
    }
};
