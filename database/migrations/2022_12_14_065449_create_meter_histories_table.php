<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeterHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meter_histories', function (Blueprint $table) {
            $table->id();
			$table->integer('vehicle_id');
			$table->string('vehicle_number')->nullable();
			$table->integer('dispatch_ticket_id')->nullable();
			$table->date("on_date")->nullable();
			$table->string('starting_km')->nullable();
			$table->string('ending_km')->nullable();
			$table->string('total_km')->nullable();
			$table->text('source')->nullable();
			$table->text('comment')->nullable();
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('meter_histories');
    }
}
