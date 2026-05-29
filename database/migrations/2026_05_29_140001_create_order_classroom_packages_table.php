<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Each order can now carry one package per classroom. The old single
 * `orders.package_id` / `package_name` / `package_price` columns are replaced
 * with this pivot, and existing data is backfilled so order history survives.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_classroom_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            // Snapshots set at submit time so the order is self-contained.
            $table->string('package_name')->nullable();
            $table->decimal('package_price', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'classroom_id']);
        });

        // Backfill: for each existing order with a package, create one pivot row
        // per distinct classroom appearing in its items (via the items' photos).
        DB::statement('
            INSERT INTO order_classroom_packages
                (order_id, classroom_id, package_id, package_name, package_price, created_at, updated_at)
            SELECT DISTINCT o.id, cp.classroom_id, o.package_id, o.package_name, o.package_price, NOW(), NOW()
            FROM orders o
            JOIN order_items oi ON oi.order_id = o.id
            JOIN classroom_photos cp ON cp.id = oi.classroom_photo_id
            WHERE o.package_id IS NOT NULL
              AND cp.classroom_id IS NOT NULL
        ');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('package_id');
            $table->dropColumn(['package_name', 'package_price']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('user_id')
                ->constrained()->nullOnDelete();
            $table->string('package_name')->nullable()->after('package_id');
            $table->decimal('package_price', 8, 2)->nullable()->after('package_name');
        });

        // Best-effort restore: copy the first pivot row per order back onto orders.
        DB::statement('
            UPDATE orders o
            JOIN (
                SELECT order_id, MIN(id) AS first_id FROM order_classroom_packages GROUP BY order_id
            ) f ON f.order_id = o.id
            JOIN order_classroom_packages p ON p.id = f.first_id
            SET o.package_id = p.package_id,
                o.package_name = p.package_name,
                o.package_price = p.package_price
        ');

        Schema::dropIfExists('order_classroom_packages');
    }
};
