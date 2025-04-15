<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->nullable();
            $table->string('licence_plate')->nullable();
            $table->string('vin_number')->nullable();
            $table->string('annual_safty_renewal')->nullable();
            $table->string('licence_plate_sticker')->nullable();
			$table->integer('total_km')->default('0');
			$table->date("last_air_filter_date")->nullable();
			$table->date("due_air_filter_date")->nullable();
            $table->integer('service_due_every_km')->default('0');
            $table->integer('air_filter_after_days')->default('0');
			$table->text('vehicle_desc')->nullable();
			$table->softDeletes();
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
        Schema::dropIfExists('vehicles');
    }
}
