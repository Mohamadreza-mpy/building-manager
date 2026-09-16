<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('description');
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->text('response')->nullable();
            $table->timestamps();
            $table->index(['apartment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
