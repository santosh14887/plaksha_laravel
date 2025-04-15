<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleAssignmentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_assignment_histories', function (Blueprint $table) {
            $table->id();
			$table->timestamp("start_time")->nullable();
			$table->timestamp("end_time")->nullable();
			$table->enum('user_vehicle_type', ['permanent','temporary'])->default('temporary');
            $table->integer('user_id')->default('0')->nullable();
			$table->string('user_type')->nullable();
			$table->string('user_name')->nullable();
			$table->string('user_email')->nullable();
			$table->text('user_phone')->nullable();
			$table->integer('vehicle_id')->default('0');
			$table->string('vehicle_number')->nullable();
			$table->string('licence_plate')->nullable();
			$table->string('vin_number')->nullable();
            $table->text('comment')->nullable();
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('vehicle_assignment_histories');
    }
}
