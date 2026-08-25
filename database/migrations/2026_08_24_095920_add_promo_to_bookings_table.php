<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('promo_id')->nullable()->constrained()->nullOnDelete()->after('venue_id');
            $table->unsignedInteger('discount_amount')->default(0)->after('total_amount');
            $table->unsignedInteger('final_amount')->storedAs('total_amount - discount_amount')->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
            $table->dropColumn(['promo_id', 'discount_amount', 'final_amount']);
        });
    }
};
