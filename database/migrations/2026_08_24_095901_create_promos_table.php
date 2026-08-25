<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete(); // null = platform-wide
            $table->string('code')->unique();
            $table->string('description');
            $table->enum('type', ['percentage', 'fixed'])->default('fixed');
            $table->unsignedInteger('value'); // percent (1-100) or fixed amount in IDR
            $table->unsignedInteger('max_uses')->nullable(); // null = unlimited
            $table->unsignedInteger('used_count')->default(0);
            $table->unsignedInteger('min_booking_amount')->default(0);
            $table->unsignedInteger('max_discount')->nullable(); // cap for percentage type
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
