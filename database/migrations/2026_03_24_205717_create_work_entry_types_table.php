<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_entry_types', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Pauza
            $table->string('code')->unique(); // break, private, work

            $table->boolean('is_paid')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_entry_types');
    }
};
