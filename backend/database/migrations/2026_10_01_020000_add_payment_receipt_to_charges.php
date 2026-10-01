<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->string('payment_receipt')->nullable()->after('paid_at');
            $table->timestamp('receipt_submitted_at')->nullable()->after('payment_receipt');
        });
    }

    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropColumn(['payment_receipt', 'receipt_submitted_at']);
        });
    }
};
