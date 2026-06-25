<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_cards', function (Blueprint $table) {
            $table->id();
            $table->string('family_no')->unique();
            $table->unsignedBigInteger('head_member_id')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->enum('status', ['active', 'inactive', 'frozen', 'deceased'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_cards');
    }
};
