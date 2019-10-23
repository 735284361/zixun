<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('teacher_id');
            $table->string('order_no')->comment('订单号');
            $table->integer('start_at')->comment('预约开始时间');
            $table->integer('end_at')->comment('预约截止时间');
            $table->integer('time_len')->comment('预约时长');
            $table->text('subject')->comment('咨询主题');
            $table->integer('status')->default(10)->comment('订单状态 10-待付款；20-已付款；');
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
        Schema::dropIfExists('zx_orders');
    }
}
