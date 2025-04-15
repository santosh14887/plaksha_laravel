<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignDispatchBrokerVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_dispatch_broker_vehicles', function (Blueprint $table) {
            $table->id();
            $table->integer('assign_dispatch_id')->default('0');
			$table->string('driver_name')->nullable();
			$table->string('vehicle_number')->nullable();
			$table->string('contact_number')->nullable();
			$table->integer('created_by')->default('0');
			$table->integer('updated_by')->default('0');
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
        Schema::dropIfExists('assign_dispatch_broker_vehicles');
    }
}
