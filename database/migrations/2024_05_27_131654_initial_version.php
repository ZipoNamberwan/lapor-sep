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
            $table->enum('type', ['Utama', 'Cadangan']);
            $table->boolean('is_selected', false);
            $table->foreignId('bs_id')->constrained('bs');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('sample_id')->nullable()->constrained('samples');
            $table->foreignId('status_id')->constrained('statuses')->default(1);
        });

        Schema::create('commodities', function (Blueprint $table) {
            $table->id()->autoincrement();
            $table->string('code');

            $table->string('name');
            $table->foreignId('sample_id')->constrained('samples')->default(1);
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
