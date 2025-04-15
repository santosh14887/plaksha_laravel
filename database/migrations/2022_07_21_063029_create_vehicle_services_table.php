<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_services', function (Blueprint $table) {
            $table->id();
			$table->integer('vehicle_id');
			$table->date("on_date")->nullable();
			$table->integer('on_km')->default('0');
			$table->integer('service_cat_id')->default('0');
			$table->text('parent_service_type')->nullable();
			$table->integer('service_subcat_id')->default('0');
			$table->text('service_type')->nullable();
			$table->decimal('expense_amount',15,2)->default('0.00')->nullable();
			$table->text('comment')->nullable();
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
        Schema::dropIfExists('vehicle_services');
    }
}
