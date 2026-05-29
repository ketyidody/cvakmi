<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Orders no longer belong to a single classroom — under the wizard flow,
     * one order can contain photos from any of the parent's classes (the
     * package covers the whole order). Each item still knows its classroom
     * via its `classroom_photo_id`.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('classroom_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->after('user_id')
                ->constrained()->nullOnDelete();
        });
    }
};
