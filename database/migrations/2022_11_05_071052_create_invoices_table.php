<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
			$table->enum('user_type', ['employee', 'customer'])->default('customer');
			$table->integer('invoice_number')->default('0');
			$table->integer('customer_id')->nullable();
			$table->integer('user_id')->nullable();
			$table->integer('quickbook_invoice_id')->nullable();
			$table->text('quickbook_invoice_res')->nullable();
			$table->integer('quickbook_payable_bill_id')->default('0');
			$table->text('quickbook_payable_bill_res')->nullable();
			$table->timestamp("invoice_date")->nullable();
			$table->text("dispatch_ids")->nullable();
			$table->text("ticket_ids")->nullable();
			$table->text("invoice_pdf")->nullable();
			$table->decimal('subtotal',15,2)->default('0.00');
			$table->integer('hst_per')->default('0');
			$table->decimal('hst_amount',15,2)->default('0.00');
			$table->decimal('total',15,2)->default('0.00');
			$table->enum('status', ['pending', 'completed'])->default('pending');
			$table->timestamp("completed_date")->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
