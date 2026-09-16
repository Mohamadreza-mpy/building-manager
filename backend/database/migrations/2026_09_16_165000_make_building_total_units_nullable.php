<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', fn (Blueprint $table) => $table->unsignedInteger('total_units')->nullable()->default(null)->change());
    }

    public function down(): void
    {
        Schema::table('buildings', fn (Blueprint $table) => $table->unsignedInteger('total_units')->default(0)->nullable(false)->change());
    }
};
