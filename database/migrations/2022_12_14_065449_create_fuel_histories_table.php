<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuelHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_histories', function (Blueprint $table) {
            $table->id();
			$table->integer('vehicle_id');
			$table->string('vehicle_number')->nullable();
			$table->integer('dispatch_ticket_id')->nullable();
			$table->date("on_date")->nullable();
			$table->decimal('fuel_qty',15,2)->default('0.00')->nullable();
			$table->integer('fuel_id')->default('0')->nullable();
			$table->string('per_liter_amount')->nullable();
			$table->date("fuel_tbl_amount_date")->nullable();
			$table->string('starting_km')->nullable();
			$table->string('ending_km')->nullable();
			$table->string('total_km')->nullable();
			$table->decimal('fuel_expense',15,2)->default('0.00')->nullable();
			$table->string('fuel_economy')->nullable();
			$table->text('fuel_receipt')->nullable();
			$table->string('fuel_card_number')->nullable();
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
