<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_print_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('print_option_id')->constrained()->cascadeOnDelete();
            // How many prints in this format the package covers.
            $table->unsignedInteger('included_quantity')->default(0);
            $table->timestamps();

            $table->unique(['package_id', 'print_option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_print_option');
    }
};
