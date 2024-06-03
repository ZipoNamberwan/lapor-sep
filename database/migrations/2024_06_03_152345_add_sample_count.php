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
        Schema::table('report_regency', function (Blueprint $table) {
            $table->integer('total_sample')->default(0);
            $table->integer('success_sample')->default(0);
        });

        Schema::table('report_subdistrict', function (Blueprint $table) {
            $table->integer('total_sample')->default(0);
            $table->integer('success_sample')->default(0);
        });

        Schema::table('report_village', function (Blueprint $table) {
            $table->integer('total_sample')->default(0);
            $table->integer('success_sample')->default(0);
        });

        Schema::table('report_bs', function (Blueprint $table) {
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
        Schema::table('report_regency', function (Blueprint $table) {
            $table->dropColumn('total_sample');
            $table->dropColumn('success_sample');
        });

        Schema::table('report_subdistrict', function (Blueprint $table) {
            $table->dropColumn('total_sample');
            $table->dropColumn('success_sample');
        });

        Schema::table('report_village', function (Blueprint $table) {
            $table->dropColumn('total_sample');
            $table->dropColumn('success_sample');
        });

        Schema::table('report_bs', function (Blueprint $table) {
            $table->dropColumn('total_sample');
            $table->dropColumn('success_sample');
        });
    }
};
