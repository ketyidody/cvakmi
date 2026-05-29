<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Distribution of an item's quantity between what the chosen
            // package covers (included_count, free) and what costs extra
            // (extra_count, charged at unit_price). Always quantity = sum.
            $table->unsignedInteger('included_count')->default(0)->after('quantity');
            $table->unsignedInteger('extra_count')->default(0)->after('included_count');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['included_count', 'extra_count']);
        });
    }
};
