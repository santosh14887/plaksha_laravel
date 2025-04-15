<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
			$table->integer('quickbook_id')->default('0');
			$table->text('quickbook_res')->nullable();
			$table->string('company_name')->nullable();
			$table->string('street_line')->nullable();
			$table->string('city')->nullable();
			$table->string('country')->nullable();
			$table->string('coutry_devision_code')->nullable()->default('CA');
			$table->string('postal_code')->nullable();
			$table->text('address')->nullable();
			$table->string('customer_hst')->nullable();
			$table->decimal('total_amount', 50,2)->default('0.00');
			$table->decimal('current_amount', 50,2)->default('0.00');
			 $table->decimal('hourly_rate', 15,2)->default('0.00');
			 $table->decimal('rate_per_load', 15,2)->default('0.00');
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
        Schema::dropIfExists('customers');
    }
}
