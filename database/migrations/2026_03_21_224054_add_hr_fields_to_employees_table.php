<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {

            if (!Schema::hasColumn('employees', 'jmbg')) {
                $table->string('jmbg')->nullable();
            }

            if (!Schema::hasColumn('employees', 'birth_date')) {
                $table->date('birth_date')->nullable();
            }

            if (!Schema::hasColumn('employees', 'employment_date')) {
                $table->date('employment_date')->nullable();
            }

            if (!Schema::hasColumn('employees', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable();
            }

            if (!Schema::hasColumn('employees', 'phone_work')) {
                $table->string('phone_work')->nullable();
            }

            if (!Schema::hasColumn('employees', 'phone_private')) {
                $table->string('phone_private')->nullable();
            }

            if (!Schema::hasColumn('employees', 'photo')) {
                $table->string('photo')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->dropColumn([
                'jmbg',
                'birth_date',
                'employment_date',
                'contract_end_date',
                'email',
                'phone_work',
                'phone_private',
                'photo'
            ]);
        });
    }
};
