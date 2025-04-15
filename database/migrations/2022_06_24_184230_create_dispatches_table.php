<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
			$table->text('default_dispatch_number')->nullable();
			$table->text('invoice_id')->nullable();
			$table->enum('invoice_sent', ['pending', 'completed'])->default('pending');
			$table->timestamp("invoice_sent_date")->nullable();
			$table->integer('customer_id');
			$table->text('customer_company_name')->nullable();
			$table->text('customer_address')->nullable();
			$table->text('customer_customer_hst')->nullable();
			$table->timestamp("start_time")->useCurrent();
			$table->string('start_location')->nullable();
			$table->string('dump_location')->nullable();
			$table->enum('job_type', ['hourly', 'load'])->default('hourly');
			$table->decimal('job_rate', 15,2)->default('0.00');
			$table->decimal('employee_rate', 15,2)->default('0.00');
			 $table->string('supervisor_name')->nullable();
			 $table->string('supervisor_contact')->nullable();
			 $table->integer('required_unit');
			 $table->enum('status', ['pending','completed','cancelled'])->default('pending');
			 $table->timestamp("completed_date")->nullable();
			 $table->timestamp("invoice_date")->nullable();
			 $table->text('comment')->nullable();
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
        Schema::dropIfExists('dispatches');
    }
}
