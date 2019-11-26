<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxRefundBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_refund_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no')->comment('订单编号');
            $table->string('refund_number')->comment('退款单号');
            $table->string('total_fee')->comment('订单总金额');
            $table->string('refund_fee')->comment('退款费用');
            $table->string('refundable_id')->comment('退款发起人ID');
            $table->string('refundable_type')->comment('退款类型');
            $table->string('return_code')->comment('订单编号');
            $table->string('return_msg')->comment('订单编号');
            $table->string('appid')->comment('订单编号');
            $table->string('mch_id')->comment('订单编号');
            $table->string('nonce_str')->comment('订单编号');
            $table->string('sign')->comment('订单编号');
            $table->string('result_code')->comment('订单编号');
            $table->string('err_code')->comment('订单编号');
            $table->string('err_code_des')->comment('订单编号');
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
        Schema::dropIfExists('zx_refund_bills');
    }
}
