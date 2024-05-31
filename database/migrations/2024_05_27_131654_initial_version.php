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
        Schema::create('regencies', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('short_code');
            $table->string('long_code')->unique();
            $table->string('name');
        });
        Schema::create('subdistricts', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('short_code');
            $table->string('long_code')->unique();
            $table->string('name');
            $table->foreignId('regency_id')->constrained('regencies');
        });
        Schema::create('villages', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('short_code');
            $table->string('long_code')->unique();
            $table->string('name');
            $table->foreignId('subdistrict_id')->constrained('subdistricts');
        });
        Schema::create('bs', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('short_code');
            $table->string('long_code')->unique();
            $table->string('nks');
            $table->string('long_nks');
            $table->string('name');
            $table->text('sls');
            $table->foreignId('village_id')->constrained('villages');
        });

        Schema::create('statuses', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('name');
            $table->string('code');
            $table->string('color');
        });

        Schema::create('samples', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->integer('no');
            $table->string('name');
            $table->string('name_p')->nullable();
            $table->enum('type', ['Utama', 'Cadangan']);
            $table->boolean('is_selected', false);
            $table->foreignId('bs_id')->constrained('bs');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('sample_id')->nullable()->constrained('samples');
            $table->foreignId('status_id')->constrained('statuses')->default(1);
            $table->nullableTimestamps();
        });

        Schema::create('commodities', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('code');

            $table->string('name');
            $table->foreignId('sample_id')->constrained('samples')->default(1);
        });

        Schema::create('report_bs', function (Blueprint $table) {
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
        });

        Schema::create('report_village', function (Blueprint $table) {
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
        });

        Schema::create('report_subdistrict', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->decimal('percentage');
            $table->string('subdistrict_short_code');
            $table->string('subdistrict_long_code');
            $table->string('subdistrict_name');
            $table->string('regency_short_code');
            $table->string('regency_long_code');
            $table->string('regency_name');
            $table->date('date');
        });

        Schema::create('report_regency', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->decimal('percentage');
            $table->string('regency_short_code');
            $table->string('regency_long_code');
            $table->string('regency_name');
            $table->date('date');
        });

        Schema::create('report_petugas', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('user_id');
            $table->string('name');
            $table->string('regency_id')->nullable();
            $table->string('status_1_count');
            $table->string('status_2_count');
            $table->string('status_3_count');
            $table->string('status_4_count');
            $table->string('status_5_count');
            $table->string('status_6_count');
            $table->string('status_7_count');
            $table->string('status_8_count');
            $table->string('status_9_count');
            $table->date('date');
        });

        Schema::create('last_update', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
