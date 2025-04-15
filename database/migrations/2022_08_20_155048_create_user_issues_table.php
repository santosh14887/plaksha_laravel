<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_issues', function (Blueprint $table) {
            $table->id();
            $table->integer('issue_category')->default('0');
            $table->integer('user_id')->default('0');
			$table->string('user_type')->nullable();
			$table->string('user_name')->nullable();
			$table->string('user_email')->nullable();
			$table->text('user_phone')->nullable();
			$table->integer('vehicle_id')->default('0');
			$table->string('vehicle_number')->nullable();
			$table->string('licence_plate')->nullable();
			$table->string('vin_number')->nullable();
			$table->string('title')->nullable();
            $table->text('description')->nullable();
			$table->enum('status', ['on_hold','pending','resolved','rejected'])->default('pending');
			$table->integer('status_no')->default('1');
			$table->timestamp("start_time")->useCurrent();
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
        Schema::dropIfExists('user_issues');
    }
}
