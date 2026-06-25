<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_body_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_body_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['support_body_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_body_member');
    }
};
