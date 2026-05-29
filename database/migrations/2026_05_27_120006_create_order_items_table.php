<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // Live references kept for convenience; nulled (not deleted) if the
            // photo or print option is later removed, so history survives.
            $table->foreignId('classroom_photo_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('print_option_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            // Snapshots taken at submit time so the order is self-contained.
            $table->string('photo_title')->nullable();
            $table->string('photo_thumbnail_path')->nullable();
            $table->string('print_option_name')->nullable();
            $table->decimal('unit_price', 8, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
