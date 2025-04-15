<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
			$table->text('default_transaction_number')->nullable();
			$table->enum('trans_genrate_type', ['system', 'extra'])->default('system');
			$table->enum('user_type', ['employee','customer','broker'])->default('employee');
			$table->enum('type', ['credit', 'debit'])->default('credit');
			$table->integer('user_id')->nullable();
			$table->integer('invoice_id')->nullable();
			$table->integer('dispatch_id')->nullable();
			$table->integer('assign_dispatch_id')->nullable();
			$table->integer('dispatch_ticket_id')->nullable();
			$table->decimal('amount', 15,2)->default('0.00');
			$table->decimal('total_amount', 15,2)->default('0.00');
			$table->text('message')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
