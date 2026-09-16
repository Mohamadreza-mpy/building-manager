<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', fn (Blueprint $table) => $table->unsignedInteger('total_units')->nullable()->default(null)->change());
    }

    public function down(): void
    {
        DB::table('buildings')->whereNull('total_units')->update(['total_units' => 0]);
        Schema::table('buildings', fn (Blueprint $table) => $table->unsignedInteger('total_units')->default(0)->nullable(false)->change());
    }
};
