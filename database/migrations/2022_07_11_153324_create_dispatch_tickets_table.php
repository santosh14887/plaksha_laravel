<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispatchTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatch_tickets', function (Blueprint $table) {
            $table->id();
			$table->text('default_ticket_number')->nullable();
			$table->integer('dispatch_id')->default('0');
			$table->integer('assign_dispatch_id')->default('0');
			$table->enum('employee_invoice_generate_status', ['pending', 'generated'])->default('pending');
			$table->text('invoice_id')->nullable();
			$table->enum('shift_type', ['day', 'night'])->default('day');
			$table->enum('user_type', ['employee', 'broker'])->default('employee');
			$table->enum('status', ['pending','completed'])->default('pending');
			$table->enum('paid_to_company', ['pending','completed'])->default('pending')->nullable();
			$table->integer('user_id')->default('0');
			$table->integer('broker_vehicle_id')->default('0');
			$table->string('driver_name')->nullable();
			$table->string('unit_vehicle_number')->nullable();
			$table->string('contact_number')->nullable();
			$table->integer('starting_km')->default('0');
			$table->integer('ending_km')->default('0');
			$table->integer('total_km')->default('0');
			$table->integer('fuel_qty')->default('0');
			$table->string('fuel_card_number')->nullable();
			$table->string('fuel_receipt')->nullable();
			$table->integer('def_qty')->default('0');
			$table->string('def_receipt')->nullable();
			$table->string('gas_station_location')->nullable();
			$table->string('ticket_number')->nullable();
			$table->string('ticket_img')->nullable();
			$table->string('hour_or_load')->nullable();
			$table->integer('hour_or_load_integer')->default('0');
			$table->integer('total_load')->default('0');
			$table->decimal('emploee_hour_over_load',15,2)->default('0.00');
			$table->decimal('emploee_hour_over_load_amount',15,2)->default('0.00');
			$table->decimal('emploee_hourly_rate_over_load',15,2)->default('0.00');
			$table->decimal('income',15,2)->default('0.00');
			$table->decimal('fuel_amount_paid',15,2)->default('0.00');
			$table->decimal('expense',15,2)->default('0.00');
			$table->decimal('expense_without_emploee_hour_over_load',15,2)->default('0.00');
			$table->decimal('profit',15,2)->default('0.00');
			$table->decimal('emp_brok_hour_rate',15,2)->default('0.00');
			$table->decimal('emp_brok_load_rate',15,2)->default('0.00');
			$table->text('remark')->nullable();
			$table->string('emp_brok_name')->nullable();
			$table->string('emp_brok_email')->nullable();
			$table->text('emp_brok_phone')->nullable();
			$table->text('emp_brok_hst')->nullable();
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
        Schema::dropIfExists('dispatch_tickets');
    }
}
