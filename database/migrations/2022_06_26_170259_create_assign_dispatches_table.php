<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_dispatches', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id')->default('0');
			$table->text('user_name')->nullable();
			$table->string('contact_number')->nullable();
			$table->integer('dispatch_id')->default('0');
			$table->integer('notification_id')->default('0');
			$table->integer('cancel_notification_id')->default('0');
			$table->integer('vehicle_id')->default('0');
			$table->string('vehicle_number')->nullable();
			$table->integer('no_of_vehicles')->default('0');
			$table->integer('no_of_vehicles_provide')->default('0');
			$table->enum('user_type', ['employee', 'broker'])->default('employee');
			$table->enum('status', ['accepted', 'decline', 'pending','completed','submitted','cancelled'])->default('pending');
			$table->integer('amount_paid')->default('0');
			$table->enum('push_notification_sent', ['yes', 'no'])->default('no');
			$table->text('push_notification_message_id')->nullable();
			$table->text('push_notification_message')->nullable();
			$table->enum('cancel_push_notification_sent', ['yes', 'no'])->default('no');
			$table->text('cancel_push_notification_message_id')->nullable();
			$table->text('cancel_push_notification_message')->nullable();
			$table->dateTime('cancel_push_notification_date')->nullable();
			$table->text('decline_reason')->nullable();
			$table->dateTime('accepted_decline_date')->nullable();
			$table->dateTime('completed_date')->nullable();
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
        Schema::dropIfExists('assign_dispatches');
    }
}
