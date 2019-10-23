<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxOrdersEval extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_orders_eval', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id')->unique();
            $table->bigInteger('user_id');
            $table->bigInteger('teacher_id');
            $table->integer('attitude')->comment('态度');
            $table->integer('speciality')->comment('专业性');
            $table->integer('satisfaction')->comment('满意度');
            $table->text('content')->nullable()->comment('评价');
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
        Schema::dropIfExists('zx_orders_eval');
    }
}
