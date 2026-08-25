<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('qr_code')->nullable()->after('notes'); // base64 QR image or token
            $table->boolean('checked_in')->default(false)->after('qr_code');
            $table->timestamp('checked_in_at')->nullable()->after('checked_in');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'checked_in', 'checked_in_at']);
        });
    }
};
