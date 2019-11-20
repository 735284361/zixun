<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxWithdrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_withdraw', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_Id')->comment('提现申请人');
            $table->string('withdraw_order')->comment('提现订单号');
            $table->double('apply_total',10,2)->comment('申请提现的金额');
            $table->tinyInteger('status')->default(10)->comment('提现状态,10表示申请提现,20表示审批通过,30交易完成,-10审批不通过.');
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
        Schema::dropIfExists('zx_withdraw');
    }
}
