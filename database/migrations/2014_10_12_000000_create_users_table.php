<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
			$table->integer('quickbook_id')->default('0');
			$table->text('quickbook_res')->nullable();
			$table->string('street_line')->nullable();
			$table->string('city')->nullable();
			$table->string('country')->nullable();
			$table->string('coutry_devision_code')->nullable()->default('CA');
			$table->string('employee_id')->nullable();
			$table->decimal('total_income', 50,2)->default('0.00');
			$table->decimal('current_amount', 50,2)->default('0.00');
            $table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('name')->nullable();
			$table->string('send_mail_on')->nullable();
			$table->decimal('hourly_rate', 15,2)->default('0.00');
			$table->decimal('load_per', 15,2)->default('0.00');
			$table->enum('type', ['admin', 'employee', 'broker','subadmin'])->default('employee');
            $table->string('email')->unique();
            $table->string('wsib_quarterly')->nullable();
            $table->string('incorporation_name')->nullable();
			$table->string('license_number')->nullable();
            $table->string('company_corporation_name')->nullable();
			$table->text('country_code')->default('+1')->nullable();
			$table->string('phone')->nullable();
			$table->string('password')->nullable();
			$table->string('password_string')->nullable();
			$table->text('address')->nullable();
			$table->string('zip_code')->nullable();
			$table->integer('vehicle_id')->default('0');
			$table->integer('available_unit')->default('0');
			$table->text('hst')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
			$table->enum('register_email_sent', ['yes', 'no'])->default('no');
			$table->enum('register_sms_sent', ['yes', 'no'])->default('no');
			$table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
