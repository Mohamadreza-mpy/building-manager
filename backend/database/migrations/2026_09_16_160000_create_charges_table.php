<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->foreignId('apartment_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->date('month');
            $table->decimal('amount', 14, 2);
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['apartment_id', 'month', 'title']);
            $table->index(['building_id', 'status', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
