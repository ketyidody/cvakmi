<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Each order belongs to exactly one classroom (per-order, per-class rule).
            // Nullable so the column can be added without backfilling demo data;
            // enforced as required when submitting an order.
            $table->foreignId('classroom_id')->nullable()->after('user_id')
                ->constrained()->nullOnDelete();
            $table->foreignId('package_id')->nullable()->after('classroom_id')
                ->constrained()->nullOnDelete();
            // Snapshot the package at submit time so the order is self-contained
            // even if the package is later edited or removed.
            $table->string('package_name')->nullable()->after('package_id');
            $table->decimal('package_price', 8, 2)->nullable()->after('package_name');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('classroom_id');
            $table->dropConstrainedForeignId('package_id');
            $table->dropColumn(['package_name', 'package_price']);
        });
    }
};
