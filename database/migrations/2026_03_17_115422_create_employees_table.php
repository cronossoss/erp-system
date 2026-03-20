<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('first_name');
            $table->string('last_name');

            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->date('hire_date')->nullable();
            $table->string('position')->nullable();

            $table->foreignId('organizational_unit_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
