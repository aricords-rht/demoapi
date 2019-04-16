<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('task_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('task_type_id');
            $table->enum('status', ['pending','ready','success','failure']);
            $table->text('request_details');
            $table->text('response_details')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('task_type_id')->references('task_type_id')->on('task_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
