<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCredsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creds_details', function (Blueprint $table) {
            $table->id();
			$table->string('quickbook_bill_payable_bank_id')->nullable();
			$table->integer('quickbook_invoice_hour_id')->default('0');
			$table->integer('quickbook_invoice_load_id')->default('0');
			$table->integer('quickbook_emp_bill_hour_id')->default('0');
			$table->integer('quickbook_emp_bill_load_id')->default('0');
			$table->string('quickbook_emp_invoice_tax_code_ref')->nullable();
			$table->integer('quickbook_emp_invoice_tax_rate_ref')->default('0');
			$table->string('quickbook_invoice_tax_code_ref')->nullable();
			$table->integer('quickbook_invoice_tax_rate_ref')->default('0');
			$table->integer('quickbook_sale_term_ref')->default('0');
			$table->enum('use_quickbook_api', ['active', 'inactive'])->default('inactive');
			$table->enum('use_own_system_opposite_quickbook', ['active', 'inactive'])->default('active');
			$table->string('creds_for')->nullable();
			$table->string('auth_mode')->nullable();
			$table->text('client_id')->nullable();
			$table->text('client_secret')->nullable();
			$table->text('redirect_uri')->nullable();
			$table->text('access_token')->nullable();
			$table->text('refresh_token')->nullable();
			$table->text('realm_id')->nullable();
			$table->enum('type', ['development', 'production'])->default('development');
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
        Schema::dropIfExists('creds_details');
    }
}
