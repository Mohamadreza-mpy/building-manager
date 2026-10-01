<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','manager','owner','resident') NOT NULL DEFAULT 'resident'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'manager', 'owner', 'resident'])->default('resident')->change();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('role')->constrained('users')->nullOnDelete();
        });

        Schema::table('apartments', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->after('building_id')->constrained('users')->nullOnDelete();
            $table->index(['owner_id', 'building_id']);
        });
    }

    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropIndex(['owner_id', 'building_id']);
            $table->dropColumn('owner_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','manager','resident') NOT NULL DEFAULT 'resident'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'manager', 'resident'])->default('resident')->change();
            });
        }
    }
};
