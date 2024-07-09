<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_bs_edcod', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->decimal('percentage');
            $table->integer('count');
            $table->string('area_code');
            $table->string('short_code');
            $table->string('village_short_code');
            $table->string('village_long_code');
            $table->string('village_name');
            $table->string('subdistrict_short_code');
            $table->string('subdistrict_long_code');
            $table->string('subdistrict_name');
            $table->string('regency_short_code');
            $table->string('regency_long_code');
            $table->string('regency_name');
            $table->date('date');
            $table->integer('total_sample')->default(0);
            $table->integer('success_sample')->default(0);
        });

        Schema::create('report_village_edcod', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->decimal('percentage');
            $table->string('village_short_code');
            $table->string('village_long_code');
            $table->string('village_name');
            $table->string('subdistrict_short_code');
            $table->string('subdistrict_long_code');
            $table->string('subdistrict_name');
            $table->string('regency_short_code');
            $table->string('regency_long_code');
            $table->string('regency_name');
            $table->date('date');
            $table->integer('total_sample')->default(0);
            $table->integer('success_sample')->default(0);
        });

        Schema::create('report_subdistrict_edcod', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->decimal('percentage');
            $table->string('subdistrict_short_code');
            $table->string('subdistrict_long_code');
            $table->string('subdistrict_name');
            $table->string('regency_short_code');
            $table->string('regency_long_code');
            $table->string('regency_name');
            $table->date('date');
            $table->integer('total_sample')->default(0);
            $table->integer('success_sample')->default(0);
        });

        Schema::create('report_regency_edcod', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->decimal('percentage');
            $table->string('regency_short_code');
            $table->string('regency_long_code');
            $table->string('regency_name');
            $table->date('date');
            $table->integer('total_sample')->default(0);
            $table->integer('success_sample')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_bs_edcod');
        Schema::dropIfExists('report_village_edcod');
        Schema::dropIfExists('report_subdistrict_edcod');
        Schema::dropIfExists('report_regency_edcod');
    }
};
