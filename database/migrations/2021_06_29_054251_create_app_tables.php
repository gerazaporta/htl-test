<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->double('price');

            $table->timestamps();
        });

        Schema::create('technician', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('truck_number');


            $table->timestamps();
        });

        Schema::create('vehicle', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->string('make');
            $table->string('model');
            $table->string('vin');

            $table->timestamps();
        });

        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('key_id');
            $table->unsignedBigInteger('technician_id');
            $table->unsignedBigInteger('vehicle_id');

            $table->timestamps();

            $table->foreign('key_id')->references('id')->on('key');
            $table->foreign('technician_id')->references('id')->on('technician');
            $table->foreign('vehicle_id')->references('id')->on('vehicle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
        Schema::dropIfExists('key');
        Schema::dropIfExists('technician');
        Schema::dropIfExists('vehicle');
    }
}
